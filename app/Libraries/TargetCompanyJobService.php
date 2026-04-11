<?php

namespace App\Libraries;

use App\Libraries\TargetCompanyCareerAiParser;

class TargetCompanyJobService
{
    // Known company → platform + slug mapping
    // Platform: greenhouse | lever | workable
    private const KNOWN_COMPANIES = [
        'stripe'        => ['platform' => 'greenhouse', 'slug' => 'stripe'],
        'shopify'       => ['platform' => 'greenhouse', 'slug' => 'shopify'],
        'airbnb'        => ['platform' => 'greenhouse', 'slug' => 'airbnb'],
        'reddit'        => ['platform' => 'greenhouse', 'slug' => 'reddit'],
        'figma'         => ['platform' => 'greenhouse', 'slug' => 'figma'],
        'notion'        => ['platform' => 'greenhouse', 'slug' => 'notion'],
        'discord'       => ['platform' => 'greenhouse', 'slug' => 'discord'],
        'dropbox'       => ['platform' => 'greenhouse', 'slug' => 'dropbox'],
        'hubspot'       => ['platform' => 'greenhouse', 'slug' => 'hubspot'],
        'intercom'      => ['platform' => 'greenhouse', 'slug' => 'intercom'],
        'linear'        => ['platform' => 'lever',      'slug' => 'linear'],
        'vercel'        => ['platform' => 'lever',      'slug' => 'vercel'],
        'supabase'      => ['platform' => 'lever',      'slug' => 'supabase'],
        'netlify'       => ['platform' => 'lever',      'slug' => 'netlify'],
        'hashicorp'     => ['platform' => 'lever',      'slug' => 'hashicorp'],
        'airtable'      => ['platform' => 'lever',      'slug' => 'airtable'],
        'remote'        => ['platform' => 'lever',      'slug' => 'remote'],
        'gitlab'        => ['platform' => 'lever',      'slug' => 'gitlab'],
    ];

    private const GENERIC_CAREER_PATHS = [
        '/careers',
        '/jobs',
        '/career',
        '/about/careers',
        '/about/jobs',
        '/careers/jobs',
        '/work-with-us',
        '/join-us',
        '/hiring',
        '/job-openings',
        '/open-positions',
    ];

    /**
     * Resolve platform and slug for a company name.
     * Returns ['platform' => ..., 'slug' => ...] or null if unknown.
     */
    public function resolveCompany(string $companyName): ?array
    {
        $key = strtolower(trim($companyName));
        return self::KNOWN_COMPANIES[$key] ?? null;
    }

    /**
     * Fetch live jobs for a company. Returns array of job objects.
     */
    public function fetchJobs(string $companyName, string $platform = '', string $slug = '', int $limit = 10): array
    {
        if ($platform === '' || $slug === '') {
            $resolved = $this->resolveCompany($companyName);
            if ($resolved) {
                $platform = $resolved['platform'];
                $slug     = $resolved['slug'];
            }
        }

        if ($platform !== '' && $slug !== '') {
            $knownJobs = $this->fetchKnownPlatformJobs($platform, $slug, $limit);
            if (!empty($knownJobs)) {
                return $knownJobs;
            }
        }

        $careerUrl = $this->discoverCareerUrl($companyName, $platform, $slug);
        if ($careerUrl === '') {
            return [];
        }

        $html = $this->getHtml($careerUrl);
        if (empty($html)) {
            return [];
        }

        $jobs = $this->extractJobsFromHtml($html, $careerUrl, $limit);
        if (!empty($jobs)) {
            return $jobs;
        }

        if ($this->hasOpenAiKey()) {
            $parser = new TargetCompanyCareerAiParser();
            $jobs = $parser->extractJobsFromPage($companyName, $careerUrl, $html, $limit);
            $jobs = $this->normalizeJobs($jobs, $careerUrl, $limit);
            if (!empty($jobs)) {
                return $jobs;
            }
        }

        if ($platform !== '' && $slug !== '') {
            return $this->fetchKnownPlatformJobs($platform, $slug, $limit);
        }

        $resolved = $this->resolveCompany($companyName);
        if ($resolved) {
            return $this->fetchKnownPlatformJobs($resolved['platform'], $resolved['slug'], $limit);
        }

        return [];
    }

    /**
     * Attempt to fetch job listings from the company career page using generic patterns and AI extraction.
     */
    private function fetchGenericCompanyJobs(string $companyName, int $limit): array
    {
        $careerUrl = $this->discoverCareerUrl($companyName);
        if ($careerUrl === '') {
            return [];
        }

        $html = $this->getHtml($careerUrl);
        if (empty($html)) {
            return [];
        }

        $parser = new TargetCompanyCareerAiParser();
        $jobs = $parser->extractJobsFromPage($companyName, $careerUrl, $html, $limit);
        if (!empty($jobs)) {
            return $jobs;
        }

        return [];
    }

    private function discoverCareerUrl(string $companyName, string $platform = '', string $slug = ''): string
    {
        if ($platform === '' || $slug === '') {
            $resolved = $this->resolveCompany($companyName);
            if ($resolved) {
                $platform = $resolved['platform'];
                $slug     = $resolved['slug'];
            }
        }

        if ($platform === 'greenhouse' && $slug !== '') {
            return 'https://boards.greenhouse.io/' . $slug;
        }

        if ($platform === 'lever' && $slug !== '') {
            return 'https://jobs.lever.co/' . $slug;
        }

        $domain = $this->buildCompanyDomain($companyName);
        $candidateUrls = $this->compileCareerCandidateUrls($domain);

        foreach ($candidateUrls as $url) {
            $html = $this->getHtml($url);
            if (empty($html)) {
                continue;
            }

            if (preg_match('/\b(job|career|opening|position|apply|opportunity)\b/i', strip_tags($html))) {
                return $url;
            }
        }

        $parser = new TargetCompanyCareerAiParser();
        $aiUrl = $parser->guessCareerPageUrl($companyName);

        return $aiUrl ?? '';
    }

    private function buildCompanyDomain(string $companyName): string
    {
        $domain = strtolower(trim($companyName));
        $domain = preg_replace('/[^a-z0-9\.]/', '', $domain);

        if ($domain === '') {
            return 'example.com';
        }

        if (strpos($domain, '.') !== false) {
            return $domain;
        }

        return $domain . '.com';
    }

    private function compileCareerCandidateUrls(string $domain): array
    {
        $bases = [
            'https://' . $domain,
            'https://www.' . $domain,
            'https://careers.' . $domain,
            'https://jobs.' . $domain,
        ];

        $urls = [];
        foreach ($bases as $base) {
            foreach (self::GENERIC_CAREER_PATHS as $path) {
                $urls[] = rtrim($base, '/') . $path;
            }
            $urls[] = $base;
        }

        return array_values(array_unique($urls));
    }

    private function getHtml(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 14,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'HireMatrix/1.0',
        ]);

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($response !== false && $status >= 200 && $status < 400) ? (string) $response : '';
    }

    private function extractJobsFromHtml(string $html, string $baseUrl, int $limit): array
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        libxml_clear_errors();

        $anchorTags = $dom->getElementsByTagName('a');
        $jobs = [];

        foreach ($anchorTags as $anchor) {
            if (!($anchor instanceof \DOMElement)) {
                continue;
            }

            $href = trim($anchor->getAttribute('href'));
            $text = trim($anchor->textContent);

            if ($href === '' || $text === '') {
                continue;
            }

            if (!preg_match('/\b(job|career|position|opening|role|apply|internship)\b/i', $href . ' ' . $text)) {
                continue;
            }

            if (preg_match('/#|^mailto:|^tel:/i', $href)) {
                continue;
            }

            $url = $this->normalizeApplyUrl($href, $baseUrl);
            if ($url === '' || isset($jobs[$url]) || !filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            if ($url === rtrim($baseUrl, '/')) {
                continue;
            }

            if ($this->isPlaceholderJobTitle($text)) {
                continue;
            }

            $jobs[$url] = [
                'id'         => $url,
                'title'      => $text,
                'location'   => 'Not specified',
                'department' => '',
                'posted_at'  => '',
                'apply_url'  => $url,
                'platform'   => 'generic',
            ];

            if (count($jobs) >= $limit) {
                break;
            }
        }

        log_message('info', 'HTML extracted ' . count($jobs) . ' jobs from ' . $baseUrl);
        return array_values($jobs);
    }

    private function isPlaceholderJobTitle(string $title): bool
    {
        $lower = strtolower($title);
        return (bool) preg_match('/\b(please visit|job search page|career page|career site|click here|learn more|find jobs|search jobs)\b/i', $lower);
    }

    private function normalizeApplyUrl(string $url, string $pageUrl): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        if (strpos($url, '//') === 0) {
            $url = 'https:' . $url;
        }

        if (parse_url($url, PHP_URL_SCHEME) !== null) {
            return $url;
        }

        return $this->resolveUrl($url, $pageUrl);
    }

    private function normalizeJobs(array $jobs, string $pageUrl, int $limit): array
    {
        $normalized = [];
        foreach ($jobs as $job) {
            if (!is_array($job)) {
                continue;
            }

            $title = trim((string) ($job['title'] ?? ''));
            $applyUrl = trim((string) ($job['apply_url'] ?? ''));
            if ($title === '' || $applyUrl === '' || $this->isPlaceholderJobTitle($title)) {
                continue;
            }

            $applyUrl = $this->normalizeApplyUrl($applyUrl, $pageUrl);
            if ($applyUrl === '' || !filter_var($applyUrl, FILTER_VALIDATE_URL)) {
                continue;
            }

            $normalized[] = [
                'id' => $applyUrl,
                'title' => $title,
                'location' => trim((string) ($job['location'] ?? 'Not specified')) ?: 'Not specified',
                'department' => trim((string) ($job['department'] ?? '')),
                'posted_at' => trim((string) ($job['posted_at'] ?? '')),
                'apply_url' => $applyUrl,
                'platform' => 'generic',
            ];

            if (count($normalized) >= $limit) {
                break;
            }
        }

        return $normalized;
    }

    private function resolveUrl(string $href, string $baseUrl): string
    {
        $href = trim($href);
        if ($href === '') {
            return '';
        }

        if (strpos($href, '//') === 0) {
            return 'https:' . $href;
        }

        if (parse_url($href, PHP_URL_SCHEME) !== null) {
            return $href;
        }

        $baseParts = parse_url($baseUrl);
        if (!isset($baseParts['scheme'], $baseParts['host'])) {
            return '';
        }

        $scheme = $baseParts['scheme'];
        $host = $baseParts['host'];
        $port = isset($baseParts['port']) ? ':' . $baseParts['port'] : '';
        $basePath = isset($baseParts['path']) ? $baseParts['path'] : '/';

        if (strpos($href, '/') === 0) {
            return $scheme . '://' . $host . $port . $href;
        }

        if (strpos($href, '?') === 0 || strpos($href, '#') === 0) {
            return $scheme . '://' . $host . $port . $basePath . $href;
        }

        $baseDir = substr($basePath, -1) === '/' ? $basePath : dirname($basePath) . '/';
        $path = $baseDir . $href;
        $resolvedParts = [];

        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }
            if ($segment === '..') {
                array_pop($resolvedParts);
                continue;
            }
            $resolvedParts[] = $segment;
        }

        return $scheme . '://' . $host . $port . '/' . implode('/', $resolvedParts);
    }

    /**
     * Fetch jobs from Greenhouse public API.
     * API: https://boards-api.greenhouse.io/v1/boards/{slug}/jobs
     */
    private function fetchGreenhouse(string $slug, int $limit): array
    {
        $url  = "https://boards-api.greenhouse.io/v1/boards/{$slug}/jobs?content=true";
        $data = $this->getJson($url);

        if (empty($data['jobs']) || !is_array($data['jobs'])) {
            return [];
        }

        $jobs = [];
        foreach (array_slice($data['jobs'], 0, $limit) as $job) {
            $jobs[] = [
                'id'         => (string) ($job['id'] ?? ''),
                'title'      => (string) ($job['title'] ?? ''),
                'location'   => (string) ($job['location']['name'] ?? 'Not specified'),
                'department'  => (string) ($job['departments'][0]['name'] ?? ''),
                'posted_at'  => (string) ($job['updated_at'] ?? ''),
                'apply_url'  => (string) ($job['absolute_url'] ?? ''),
                'platform'   => 'greenhouse',
            ];
        }

        return $jobs;
    }

    /**
     * Fetch jobs from Lever public API.
     * API: https://api.lever.co/v0/postings/{slug}
     */
    private function fetchLever(string $slug, int $limit): array
    {
        $url  = "https://api.lever.co/v0/postings/{$slug}?mode=json&limit={$limit}";
        $data = $this->getJson($url);

        if (!is_array($data)) {
            return [];
        }

        $jobs = [];
        foreach (array_slice($data, 0, $limit) as $job) {
            $jobs[] = [
                'id'        => (string) ($job['id'] ?? ''),
                'title'     => (string) ($job['text'] ?? ''),
                'location'  => (string) ($job['categories']['location'] ?? 'Not specified'),
                'department' => (string) ($job['categories']['department'] ?? ''),
                'posted_at' => isset($job['createdAt']) ? date('Y-m-d', (int) ($job['createdAt'] / 1000)) : '',
                'apply_url' => (string) ($job['hostedUrl'] ?? ''),
                'platform'  => 'lever',
            ];
        }

        return $jobs;
    }

    /**
     * Simple HTTP GET returning decoded JSON array.
     */
    private function getJson(string $url): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => ['Accept: application/json', 'User-Agent: HireMatrix/1.0'],
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $status !== 200) {
            return [];
        }

        $decoded = json_decode((string) $response, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function fetchKnownPlatformJobs(string $platform, string $slug, int $limit): array
    {
        if ($platform === 'greenhouse') {
            return $this->fetchGreenhouse($slug, $limit);
        }

        if ($platform === 'lever') {
            return $this->fetchLever($slug, $limit);
        }

        return [];
    }

    private function hasOpenAiKey(): bool
    {
        $parser = new TargetCompanyCareerAiParser();
        return method_exists($parser, 'hasApiKey') ? $parser->hasApiKey() : false;
    }

    /**
     * Check if a company is in the known list.
     */
    public function isKnownCompany(string $companyName): bool
    {
        return isset(self::KNOWN_COMPANIES[strtolower(trim($companyName))]);
    }

    /**
     * Return list of all known company names for autocomplete.
     */
    public function getKnownCompanyNames(): array
    {
        return array_map('ucfirst', array_keys(self::KNOWN_COMPANIES));
    }
}