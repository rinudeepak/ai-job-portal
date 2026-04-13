<?php

namespace App\Libraries;

use App\Models\CompanyAtsMappingModel;
use App\Libraries\TargetCompanyCareerAiParser;

class TargetCompanyJobService
{
    // Known company → platform + slug mapping
    // Platform: greenhouse | lever | smartrecruiters | workday | taleo | successfactors | icims | tcs
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
        'tcs'           => ['platform' => 'tcs',        'slug' => 'tcs'],
    ];

    private const DEFAULT_COMPANY_MAPPINGS = [
        'wipro' => [
            'company_name' => 'Wipro',
            'company_key' => 'wipro',
            'aliases' => 'Wipro Limited|Wipro Technologies',
            'platform' => 'generic',
            'platform_slug' => '',
            'career_url' => 'https://www.wipro.com/en-US/wipro-in-us/',
            'website_url' => 'https://www.wipro.com/',
        ],
        'infosys' => [
            'company_name' => 'Infosys',
            'company_key' => 'infosys',
            'aliases' => 'Infosys Limited|Infosys Ltd',
            'platform' => 'generic',
            'platform_slug' => '',
            'career_url' => 'https://digitalcareers.infosys.com/infosys/global-careers',
            'website_url' => 'https://www.infosys.com/',
        ],
        'tcs' => [
            'company_name' => 'TCS',
            'company_key' => 'tcs',
            'aliases' => 'Tata Consultancy Services|Tata Consultancy Services Limited',
            'platform' => 'generic',
            'platform_slug' => '',
            'career_url' => 'https://www.tcs.com/careers',
            'website_url' => 'https://www.tcs.com/',
        ],
        'accenture' => [
            'company_name' => 'Accenture',
            'company_key' => 'accenture',
            'aliases' => 'Accenture plc',
            'platform' => 'workday',
            'platform_slug' => '',
            'career_url' => 'https://www.accenture.com/us-en/careers',
            'website_url' => 'https://www.accenture.com/',
        ],
        'cognizant' => [
            'company_name' => 'Cognizant',
            'company_key' => 'cognizant',
            'aliases' => 'Cognizant Technology Solutions',
            'platform' => 'generic',
            'platform_slug' => '',
            'career_url' => 'https://careers.cognizant.com/global-en/jobs/',
            'website_url' => 'https://www.cognizant.com/',
        ],
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

    public function resolveCompanySource(string $companyName): ?array
    {
        $mapping = $this->findCompanyMapping($companyName);
        if ($mapping !== null) {
            return $mapping;
        }

        $resolved = $this->resolveCompany($companyName);
        if ($resolved !== null) {
            return [
                'company_name' => trim($companyName),
                'company_key' => $this->buildSlugFromCompanyName($companyName),
                'aliases' => '',
                'platform' => $resolved['platform'],
                'platform_slug' => $resolved['slug'],
                'career_url' => '',
                'website_url' => '',
            ];
        }

        return null;
    }

    /**
     * Fetch live jobs for a company. Returns array of job objects.
     */
    public function fetchJobs(string $companyName, string $platform = '', string $slug = '', int $limit = 10): array
    {
        $source = $this->resolveCompanySource($companyName);
        if ($source !== null) {
            $platform = $platform !== '' ? $platform : (string) ($source['platform'] ?? '');
            $slug = $slug !== '' ? $slug : (string) ($source['platform_slug'] ?? '');
        }

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

        $careerUrl = $this->discoverCareerUrl($companyName, $platform, $slug, $source);
        if ($careerUrl === '') {
            return [];
        }

        $careerHtml = $this->getHtml($careerUrl);
        if (empty($careerHtml)) {
            return [];
        }

        $detectedPlatform = $platform !== '' ? $platform : $this->detectPlatformFromUrl($careerUrl);
        $detectedPlatform = $detectedPlatform !== '' ? $detectedPlatform : $this->detectPlatformFromHtml($careerHtml, $careerUrl);

        if ($detectedPlatform !== '') {
            $atsJobs = $this->fetchPlatformJobsFromCareerUrl($detectedPlatform, $companyName, $careerUrl, $slug, $limit);
            if (!empty($atsJobs)) {
                return $atsJobs;
            }
        }

        $structuredJobs = $this->extractJobsFromStructuredData($careerHtml, $careerUrl, $limit, 'generic');
        if (!empty($structuredJobs)) {
            return $structuredJobs;
        }

        $jobs = $this->extractJobsFromHtml($careerHtml, $careerUrl, $limit);
        if (!empty($jobs)) {
            return $jobs;
        }

        if ($this->hasOpenAiKey()) {
            $parser = new TargetCompanyCareerAiParser();
            $jobs = $parser->extractJobsFromPage($companyName, $careerUrl, $careerHtml, $limit);
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

    public function fetchCompanyDetails(string $companyName): array
    {
        $companyInfo = [
            'name' => trim($companyName),
            'description' => '',
            'website' => '',
            'career_page' => '',
            'linkedin' => '',
            'twitter' => '',
            'facebook' => '',
            'instagram' => '',
            'youtube' => '',
        ];

        $source = $this->resolveCompanySource($companyName);
        $careerUrl = $this->discoverCareerUrl($companyName, $source['platform'] ?? '', $source['platform_slug'] ?? '', $source);
        $parser = new TargetCompanyCareerAiParser();

        if ($careerUrl !== '') {
            $companyInfo['career_page'] = $careerUrl;
            $careerHtml = $this->getHtml($careerUrl);
            if (!empty($careerHtml)) {
                $aiInfo = $parser->extractCompanyInfoFromPage($companyName, $careerUrl, $careerHtml);
                if (!empty($aiInfo)) {
                    $companyInfo = array_merge($companyInfo, array_filter($aiInfo, static fn ($value) => $value !== ''));
                }
            }
        }

        $websiteUrl = trim((string) ($companyInfo['website'] ?? ''));
        if ($websiteUrl === '' && $source !== null && !empty($source['website_url'])) {
            $websiteUrl = trim((string) $source['website_url']);
        }
        if ($websiteUrl === '') {
            $websiteUrl = 'https://' . $this->buildCompanyDomain($companyName);
        }

        if ($websiteUrl !== '') {
            $homeHtml = $this->getHtml($websiteUrl);
            if (!empty($homeHtml)) {
                $homeInfo = $parser->extractCompanyInfoFromPage($companyName, $websiteUrl, $homeHtml);
                if (!empty($homeInfo)) {
                    $companyInfo = array_merge($companyInfo, array_filter($homeInfo, static fn ($value) => $value !== ''));
                }
            }
        }

        if ($companyInfo['website'] === '') {
            $companyInfo['website'] = $websiteUrl;
        }

        if ($companyInfo['career_page'] === '') {
            $companyInfo['career_page'] = $careerUrl;
        }

        return $companyInfo;
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

    private function discoverCareerUrl(string $companyName, string $platform = '', string $slug = '', ?array $source = null): string
    {
        if ($source !== null) {
            $mappedCareerUrl = trim((string) ($source['career_url'] ?? ''));
            if ($mappedCareerUrl !== '') {
                $mappedHtml = $this->getHtml($mappedCareerUrl);
                if (!empty($mappedHtml)) {
                    $linkedCareerUrl = $this->extractCareerLinkFromHtml($mappedHtml, $mappedCareerUrl);
                    $mappedDetected = $this->detectPlatformFromUrl($mappedCareerUrl);
                    $mappedDetected = $mappedDetected !== '' ? $mappedDetected : $this->detectPlatformFromHtml($mappedHtml, $mappedCareerUrl);
                    if (
                        $mappedDetected !== '' ||
                        preg_match('/\b(job|career|opening|position|apply|opportunity)\b/i', strip_tags($mappedHtml))
                    ) {
                        return $mappedCareerUrl;
                    }

                    if ($linkedCareerUrl !== '' && $linkedCareerUrl !== $mappedCareerUrl) {
                        return $linkedCareerUrl;
                    }
                }
            }
        }

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

        $domain = '';
        if ($source !== null) {
            $mappedWebsiteUrl = trim((string) ($source['website_url'] ?? ''));
            if ($mappedWebsiteUrl !== '') {
                $domain = (string) parse_url($mappedWebsiteUrl, PHP_URL_HOST);
            }
        }

        if ($domain === '') {
            $domain = $this->buildCompanyDomain($companyName);
        }

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

        $homepageUrl = 'https://' . $domain;
        $homepageHtml = $this->getHtml($homepageUrl);
        if (!empty($homepageHtml)) {
            $linkedCareerUrl = $this->extractCareerLinkFromHtml($homepageHtml, $homepageUrl);
            if ($linkedCareerUrl !== '') {
                return $linkedCareerUrl;
            }
        }

        $parser = new TargetCompanyCareerAiParser();
        $aiUrl = $parser->guessCareerPageUrl($companyName);

        return $aiUrl ?? '';
    }

    private function findCompanyMapping(string $companyName): ?array
    {
        $companyName = trim($companyName);
        if ($companyName === '') {
            return null;
        }

        $db = \Config\Database::connect();
        if ($db->tableExists('company_ats_mappings')) {
            try {
                $model = new CompanyAtsMappingModel();
                $match = $model->findMatchingMapping($companyName);
                if (is_array($match) && !empty($match)) {
                    return $this->normalizeCompanyMappingRow($match);
                }
            } catch (\Throwable $e) {
                log_message('warning', 'Company ATS mapping lookup failed: ' . $e->getMessage());
            }
        }

        return $this->findDefaultCompanyMapping($companyName);
    }

    private function normalizeCompanyMappingRow(array $row): array
    {
        return [
            'company_name' => trim((string) ($row['company_name'] ?? '')),
            'company_key' => trim((string) ($row['company_key'] ?? '')),
            'aliases' => trim((string) ($row['aliases'] ?? '')),
            'platform' => trim((string) ($row['platform'] ?? '')),
            'platform_slug' => trim((string) ($row['platform_slug'] ?? '')),
            'career_url' => trim((string) ($row['career_url'] ?? '')),
            'website_url' => trim((string) ($row['website_url'] ?? '')),
            'notes' => trim((string) ($row['notes'] ?? '')),
        ];
    }

    private function findDefaultCompanyMapping(string $companyName): ?array
    {
        $key = $this->buildSlugFromCompanyName($companyName);
        $matchKey = $this->normalizeCompanyMatchKey($companyName);
        if ($key === '') {
            return null;
        }

        foreach (self::DEFAULT_COMPANY_MAPPINGS as $defaultKey => $mapping) {
            $companyKeys = array_filter([
                $defaultKey,
                (string) ($mapping['company_key'] ?? ''),
                (string) ($mapping['company_name'] ?? ''),
            ]);

            foreach ($companyKeys as $companyKey) {
                if ($companyKey !== '' && ($companyKey === $key || $this->normalizeCompanyMatchKey($companyKey) === $matchKey)) {
                    return $mapping;
                }
            }

            $aliases = preg_split('/[|,\r\n;]+/', (string) ($mapping['aliases'] ?? '')) ?: [];
            foreach ($aliases as $alias) {
                $alias = trim($alias);
                if ($alias === '') {
                    continue;
                }

                if ($this->normalizeCompanyMatchKey($alias) === $matchKey) {
                    return $mapping;
                }
            }
        }

        return null;
    }

    private function extractCareerLinkFromHtml(string $html, string $baseUrl): string
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $patterns = [
            '/\b(search jobs|job search|open roles|open positions|browse all jobs|explore all jobs|view jobs)\b/i',
            '/\b(career|careers|jobs|job|join us|join-us|work with us|vacancies)\b/i',
        ];

        foreach ($patterns as $pattern) {
            foreach ($xpath->query('//a[@href]') as $node) {
                $text = strtolower(trim((string) $node->textContent));
                $href = trim((string) $node->getAttribute('href'));
                if ($href === '') {
                    continue;
                }

                $combined = strtolower($text . ' ' . $href);
                if (!preg_match($pattern, $combined)) {
                    continue;
                }

                $url = $this->normalizeApplyUrl($href, $baseUrl);
                if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
                    return $url;
                }
            }
        }

        return '';
    }

    private function detectPlatformFromUrl(string $url): string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = strtolower((string) parse_url($url, PHP_URL_PATH));
        $haystack = $host . ' ' . $path;

        if (str_contains($haystack, 'greenhouse')) {
            return 'greenhouse';
        }
        if (str_contains($haystack, 'lever.co')) {
            return 'lever';
        }
        if (str_contains($haystack, 'smartrecruiters')) {
            return 'smartrecruiters';
        }
        if (str_contains($haystack, 'myworkdayjobs') || str_contains($haystack, 'workday')) {
            return 'workday';
        }
        if (str_contains($haystack, 'taleo') || str_contains($haystack, 'oraclecloud')) {
            return 'taleo';
        }
        if (str_contains($haystack, 'successfactors') || str_contains($haystack, 'jobs.sap.com')) {
            return 'successfactors';
        }
        if (str_contains($haystack, 'sapsf') || str_contains($haystack, 'rmk-jobs-search') || str_contains($haystack, 'j2w.search')) {
            return 'successfactors';
        }
        if (str_contains($haystack, 'icims')) {
            return 'icims';
        }

        return '';
    }

    private function detectPlatformFromHtml(string $html, string $url = ''): string
    {
        $haystack = strtolower($url . "\n" . $html);
        if ($haystack === '') {
            return '';
        }

        if (str_contains($haystack, 'smartrecruiters')) {
            return 'smartrecruiters';
        }
        if (str_contains($haystack, 'myworkdayjobs') || str_contains($haystack, 'workday')) {
            return 'workday';
        }
        if (str_contains($haystack, 'successfactors') || str_contains($haystack, 'jobs.sap.com')) {
            return 'successfactors';
        }
        if (str_contains($haystack, 'sapsf') || str_contains($haystack, 'rmk-jobs-search') || str_contains($haystack, 'j2w.search')) {
            return 'successfactors';
        }
        if (str_contains($haystack, 'icims')) {
            return 'icims';
        }
        if (str_contains($haystack, 'taleo') || str_contains($haystack, 'oraclecloud')) {
            return 'taleo';
        }

        return '';
    }

    private function fetchPlatformJobsFromCareerUrl(string $platform, string $companyName, string $careerUrl, string $slug, int $limit): array
    {
        switch ($platform) {
            case 'greenhouse':
                return $this->fetchGreenhouseFromCareerUrl($careerUrl, $limit);
            case 'lever':
                return $this->fetchLeverFromCareerUrl($careerUrl, $limit);
            case 'smartrecruiters':
                return $this->fetchSmartRecruitersFromCareerUrl($careerUrl, $companyName, $slug, $limit);
            case 'workday':
                return $this->fetchWorkdayFromCareerUrl($careerUrl, $companyName, $limit);
            case 'taleo':
                return $this->fetchTaleoFromCareerUrl($careerUrl, $companyName, $limit);
            case 'successfactors':
                return $this->fetchSuccessFactorsFromCareerUrl($careerUrl, $companyName, $limit);
            case 'icims':
                return $this->fetchIcimsFromCareerUrl($careerUrl, $companyName, $limit);
            default:
                return [];
        }
    }

    private function fetchGreenhouseFromCareerUrl(string $careerUrl, int $limit): array
    {
        $slug = $this->extractGreenhouseSlug($careerUrl);
        if ($slug === '') {
            return [];
        }

        return $this->fetchGreenhouse($slug, $limit);
    }

    private function extractGreenhouseSlug(string $careerUrl): string
    {
        $host = strtolower((string) parse_url($careerUrl, PHP_URL_HOST));
        $path = trim((string) parse_url($careerUrl, PHP_URL_PATH), '/');

        if (str_contains($host, 'greenhouse.io')) {
            $parts = array_values(array_filter(explode('/', $path)));
            return $parts[0] ?? '';
        }

        return '';
    }

    private function fetchLeverFromCareerUrl(string $careerUrl, int $limit): array
    {
        $slug = $this->extractLeverSlug($careerUrl);
        if ($slug === '') {
            return [];
        }

        return $this->fetchLever($slug, $limit);
    }

    private function extractLeverSlug(string $careerUrl): string
    {
        $host = strtolower((string) parse_url($careerUrl, PHP_URL_HOST));
        $path = trim((string) parse_url($careerUrl, PHP_URL_PATH), '/');

        if (str_contains($host, 'lever.co')) {
            $parts = array_values(array_filter(explode('/', $path)));
            return $parts[0] ?? '';
        }

        return '';
    }

    private function fetchSmartRecruitersFromCareerUrl(string $careerUrl, string $companyName, string $slug, int $limit): array
    {
        $smartSlug = $slug !== '' ? $slug : $this->extractSmartRecruitersSlug($careerUrl);
        if ($smartSlug === '') {
            $smartSlug = $this->buildSlugFromCompanyName($companyName);
        }

        if ($smartSlug !== '') {
            $apiUrl = 'https://api.smartrecruiters.com/v1/companies/' . rawurlencode($smartSlug) . '/jobs?limit=' . $limit;
            $data = $this->getJson($apiUrl);
            $jobs = $this->normalizeJobsFromAnyPayload($data, $careerUrl, $limit, 'smartrecruiters');
            if (!empty($jobs)) {
                return $jobs;
            }
        }

        return $this->fetchJobsFromHtmlOrAi($companyName, $careerUrl, $limit, 'smartrecruiters');
    }

    private function extractSmartRecruitersSlug(string $careerUrl): string
    {
        $host = strtolower((string) parse_url($careerUrl, PHP_URL_HOST));
        $path = trim((string) parse_url($careerUrl, PHP_URL_PATH), '/');

        if (!str_contains($host, 'smartrecruiters.com')) {
            return '';
        }

        $parts = array_values(array_filter(explode('/', $path)));
        foreach ($parts as $part) {
            if (!in_array($part, ['jobs', 'company', 'careers'], true)) {
                return $part;
            }
        }

        return '';
    }

    private function fetchWorkdayFromCareerUrl(string $careerUrl, string $companyName, int $limit): array
    {
        $html = $this->getHtml($careerUrl);
        if (empty($html)) {
            return [];
        }

        $apiUrls = $this->extractWorkdayApiUrls($html);
        foreach ($apiUrls as $apiUrl) {
            $data = $this->getJson($apiUrl);
            $jobs = $this->normalizeJobsFromAnyPayload($data, $careerUrl, $limit, 'workday');
            if (!empty($jobs)) {
                return $jobs;
            }
        }

        $jobs = $this->extractJobsFromStructuredData($html, $careerUrl, $limit, 'workday');
        if (!empty($jobs)) {
            return $jobs;
        }

        $jobs = $this->extractJobsFromHtml($html, $careerUrl, $limit);
        if (!empty($jobs)) {
            return $jobs;
        }

        return $this->fetchJobsFromHtmlOrAi($companyName, $careerUrl, $limit, 'workday', $html);
    }

    private function extractWorkdayApiUrls(string $html): array
    {
        $urls = [];
        if (preg_match_all('#https://[a-z0-9.-]+myworkdayjobs\.com/wday/cxs/[^"\']+/jobs[^"\']*#i', $html, $matches)) {
            $urls = array_merge($urls, $matches[0]);
        }

        if (preg_match_all('#/wday/cxs/[^"\']+/jobs[^"\']*#i', $html, $matches)) {
            foreach ($matches[0] as $path) {
                $urls[] = 'https://' . $this->extractWorkdayHost($html) . $path;
            }
        }

        return array_values(array_unique(array_filter($urls)));
    }

    private function extractWorkdayHost(string $html): string
    {
        if (preg_match('#https://([a-z0-9.-]+myworkdayjobs\.com)#i', $html, $matches)) {
            return $matches[1];
        }

        return '';
    }

    private function postJson(string $url, array $payload): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'User-Agent: HireMatrix/1.0',
            ],
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $status < 200 || $status >= 400) {
            return [];
        }

        $decoded = json_decode((string) $response, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function normalizeSuccessFactorsJobs(array $payload, string $baseUrl, string $locale, int $limit): array
    {
        $items = $payload['jobSearchResult'] ?? [];
        if (!is_array($items)) {
            return [];
        }

        $jobs = [];
        foreach (array_slice($items, 0, $limit) as $item) {
            if (!is_array($item)) {
                continue;
            }

            $job = is_array($item['response'] ?? null) ? $item['response'] : $item;
            $title = trim((string) (
                $job['unifiedStandardTitle'] ??
                $job['title'] ??
                $job['jobTitle'] ??
                $job['name'] ??
                ''
            ));
            $id = trim((string) ($job['id'] ?? ''));
            $urlTitle = trim((string) ($job['unifiedUrlTitle'] ?? $job['urlTitle'] ?? ''));

            if ($title === '' || $id === '' || $urlTitle === '') {
                continue;
            }

            $applyUrl = rtrim($baseUrl, '/') . '/job/' . $urlTitle . '/' . $id . '-' . $locale;
            if (!filter_var($applyUrl, FILTER_VALIDATE_URL)) {
                continue;
            }

            $location = '';
            if (!empty($job['sfstd_jobLocation_obj']) && is_array($job['sfstd_jobLocation_obj'])) {
                $location = trim((string) implode(', ', array_filter(array_map('trim', $job['sfstd_jobLocation_obj']))));
            }
            if ($location === '' && !empty($job['jobLocationShort']) && is_array($job['jobLocationShort'])) {
                $location = trim(strip_tags((string) ($job['jobLocationShort'][0] ?? '')));
            }
            if ($location === '') {
                $location = 'Not specified';
            }

            $department = '';
            if (!empty($job['custRMKMappingPicklist']) && is_array($job['custRMKMappingPicklist'])) {
                $department = trim((string) ($job['custRMKMappingPicklist'][0] ?? ''));
            }

            $jobs[$applyUrl] = [
                'id' => $id,
                'title' => $title,
                'location' => $location,
                'department' => $department,
                'posted_at' => trim((string) ($job['unifiedStandardStart'] ?? $job['datePosted'] ?? '')),
                'apply_url' => $applyUrl,
                'platform' => 'successfactors',
                'employment_type' => trim((string) ($job['jobType'] ?? '')),
                'description' => trim((string) ($job['jobDescription'] ?? $job['description'] ?? '')),
            ];
        }

        return array_values($jobs);
    }

    private function fetchTaleoFromCareerUrl(string $careerUrl, string $companyName, int $limit): array
    {
        $html = $this->getHtml($careerUrl);
        if (empty($html)) {
            return [];
        }

        $jobs = $this->extractJobsFromStructuredData($html, $careerUrl, $limit, 'taleo');
        if (!empty($jobs)) {
            return $jobs;
        }

        $jobs = $this->extractJobsFromHtml($html, $careerUrl, $limit);
        if (!empty($jobs)) {
            return $jobs;
        }

        return $this->fetchJobsFromHtmlOrAi($companyName, $careerUrl, $limit, 'taleo', $html);
    }

    private function fetchSuccessFactorsFromCareerUrl(string $careerUrl, string $companyName, int $limit): array
    {
        $host = (string) parse_url($careerUrl, PHP_URL_HOST);
        $host = strtolower(trim($host));
        if ($host === '') {
            return [];
        }

        $locale = 'en_US';
        $jobs = [];
        $pageNumber = 0;
        $pages = max(1, (int) ceil($limit / 25));

        for ($page = 0; $page < $pages; $page++) {
            $apiUrl = 'https://' . $host . '/services/recruiting/v1/jobs';
            $payload = [
                'keywords' => '',
                'locale' => $locale,
                'location' => '',
                'pageNumber' => $pageNumber,
                'sortBy' => 'recent',
            ];

            $data = $this->postJson($apiUrl, $payload);
            $pageJobs = $this->normalizeSuccessFactorsJobs($data, $careerUrl, $locale, $limit);
            foreach ($pageJobs as $job) {
                $jobs[$job['apply_url']] = $job;
                if (count($jobs) >= $limit) {
                    break 2;
                }
            }

            if (empty($data['jobSearchResult'] ?? [])) {
                break;
            }

            $pageNumber++;
        }

        if (!empty($jobs)) {
            return array_values(array_slice($jobs, 0, $limit));
        }

        $html = $this->getHtml($careerUrl);
        if (!empty($html)) {
            $linkedCareerUrl = $this->extractCareerLinkFromHtml($html, $careerUrl);
            if ($linkedCareerUrl !== '' && $linkedCareerUrl !== $careerUrl) {
                return $this->fetchSuccessFactorsFromCareerUrl($linkedCareerUrl, $companyName, $limit);
            }
        }

        return $this->fetchJobsFromHtmlOrAi($companyName, $careerUrl, $limit, 'successfactors', $html ?: null);
    }

    private function fetchIcimsFromCareerUrl(string $careerUrl, string $companyName, int $limit): array
    {
        $html = $this->getHtml($careerUrl);
        if (empty($html)) {
            return [];
        }

        $jobs = $this->extractJobsFromStructuredData($html, $careerUrl, $limit, 'icims');
        if (!empty($jobs)) {
            return $jobs;
        }

        $jobs = $this->extractJobsFromHtml($html, $careerUrl, $limit);
        if (!empty($jobs)) {
            return $jobs;
        }

        return $this->fetchJobsFromHtmlOrAi($companyName, $careerUrl, $limit, 'icims', $html);
    }

    private function fetchJobsFromHtmlOrAi(string $companyName, string $careerUrl, int $limit, string $platform, ?string $html = null): array
    {
        $html = $html ?? $this->getHtml($careerUrl);
        if (empty($html)) {
            return [];
        }

        $jobs = $this->extractJobsFromStructuredData($html, $careerUrl, $limit, $platform);
        if (!empty($jobs)) {
            return $jobs;
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

        return [];
    }

    private function extractJobsFromStructuredData(string $html, string $baseUrl, int $limit, string $platform): array
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $jobs = [];

        foreach ($xpath->query('//script[@type="application/ld+json"]') as $node) {
            $json = trim((string) $node->textContent);
            if ($json === '') {
                continue;
            }

            $decoded = json_decode($json, true);
            if (!is_array($decoded)) {
                continue;
            }

            $this->collectJobsFromArray($decoded, $jobs, $baseUrl, $platform, $limit);
            if (count($jobs) >= $limit) {
                break;
            }
        }

        return array_values($jobs);
    }

    private function collectJobsFromArray(array $payload, array &$jobs, string $baseUrl, string $platform, int $limit): void
    {
        if (count($jobs) >= $limit) {
            return;
        }

        if ($this->looksLikeJobItem($payload)) {
            $job = $this->normalizeGenericJobItem($payload, $baseUrl, $platform);
            if ($job !== null && !isset($jobs[$job['apply_url']])) {
                $jobs[$job['apply_url']] = $job;
            }
        }

        foreach ($payload as $value) {
            if (count($jobs) >= $limit) {
                break;
            }
            if (is_array($value)) {
                $this->collectJobsFromArray($value, $jobs, $baseUrl, $platform, $limit);
            }
        }
    }

    private function looksLikeJobItem(array $payload): bool
    {
        $keys = array_map(static fn ($key): string => strtolower((string) $key), array_keys($payload));
        $titleKeys = ['title', 'jobtitle', 'name', 'headline', 'position'];
        $urlKeys = ['applyurl', 'apply_url', 'url', 'externalpath', 'absolute_url', 'hostedurl', 'joburl', 'job_url'];

        foreach ($keys as $key) {
            foreach ($titleKeys as $needle) {
                if ($key === $needle || str_contains($key, $needle)) {
                    foreach ($keys as $urlKey) {
                        foreach ($urlKeys as $urlNeedle) {
                            if ($urlKey === $urlNeedle || str_contains($urlKey, $urlNeedle)) {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    private function normalizeGenericJobItem(array $job, string $baseUrl, string $platform): ?array
    {
        $title = trim((string) ($job['title'] ?? $job['text'] ?? $job['jobTitle'] ?? $job['name'] ?? ''));
        $applyUrl = trim((string) (
            $job['apply_url'] ??
            $job['applyUrl'] ??
            $job['absolute_url'] ??
            $job['hostedUrl'] ??
            $job['url'] ??
            $job['externalPath'] ??
            $job['jobUrl'] ??
            ''
        ));

        if ($title === '' || $applyUrl === '') {
            return null;
        }

        $applyUrl = $this->normalizeApplyUrl($applyUrl, $baseUrl);
        if ($applyUrl === '' || !filter_var($applyUrl, FILTER_VALIDATE_URL) || $this->isPlaceholderJobTitle($title)) {
            return null;
        }

        $location = trim((string) (
            $job['location'] ??
            $job['locationName'] ??
            $job['locationsText'] ??
            $job['city'] ??
            $job['place'] ??
            'Not specified'
        ));
        $department = trim((string) ($job['department'] ?? $job['departmentName'] ?? ''));
        $postedAt = trim((string) ($job['postedAt'] ?? $job['posted_at'] ?? $job['datePosted'] ?? $job['updatedAt'] ?? ''));

        return [
            'id' => $applyUrl,
            'title' => $title,
            'location' => $location !== '' ? $location : 'Not specified',
            'department' => $department,
            'posted_at' => $postedAt,
            'apply_url' => $applyUrl,
            'platform' => $platform,
        ];
    }

    private function normalizeJobsFromAnyPayload(array $payload, string $baseUrl, int $limit, string $platform): array
    {
        $jobs = [];
        $this->collectJobsFromArray($payload, $jobs, $baseUrl, $platform, $limit);
        return array_values(array_slice($jobs, 0, $limit));
    }

    private function buildSlugFromCompanyName(string $companyName): string
    {
        $slug = strtolower(trim($companyName));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }

    private function normalizeCompanyMatchKey(string $companyName): string
    {
        $key = strtolower(trim($companyName));
        $key = preg_replace('/[^a-z0-9]+/', ' ', $key) ?? '';
        $key = preg_replace('/\b(limited|ltd|inc|llc|llp|plc|corp|corporation|company|co|technologies|technology|solutions|services|systems|group|holdings)\b/', ' ', $key) ?? '';
        $key = preg_replace('/\s+/', ' ', $key) ?? '';
        return trim($key);
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
        return (bool) preg_match('/\b(please visit|job search page|career page|career site|click here|learn more|find jobs|search jobs|apply now|apply here|recruitment fraud|fraud alert|beware of fraud|view all jobs|see all jobs|all openings|load more|show more|back to top|cookie|privacy policy|terms of use|growing your career|view global openings|global openings|explore opportunities|explore careers|explore jobs|join our team|life at|working at|why work|our culture|meet our team|employee stories|diversity|inclusion|benefits|perks|early careers|students|graduates|veterans|accessibility)\b/i', $lower);
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
     * Fetch jobs from SmartRecruiters public API or fallback to HTML extraction.
     * API: https://api.smartrecruiters.com/v1/companies/{company}/jobs
     */
    private function fetchSmartRecruiters(string $slug, int $limit): array
    {
        $slug = trim($slug);
        if ($slug === '') {
            return [];
        }

        $apiUrl = 'https://api.smartrecruiters.com/v1/companies/' . rawurlencode($slug) . '/jobs?limit=' . $limit;
        $data = $this->getJson($apiUrl);
        $jobs = $this->normalizeSmartRecruitersJobs($data, $limit);
        if (!empty($jobs)) {
            return $jobs;
        }

        return [];
    }

    private function normalizeSmartRecruitersJobs(array $payload, int $limit): array
    {
        $items = [];
        if (isset($payload['content']) && is_array($payload['content'])) {
            $items = $payload['content'];
        } elseif (isset($payload['jobs']) && is_array($payload['jobs'])) {
            $items = $payload['jobs'];
        } elseif (array_is_list($payload)) {
            $items = $payload;
        }

        $jobs = [];
        foreach (array_slice($items, 0, $limit) as $job) {
            if (!is_array($job)) {
                continue;
            }

            $title = trim((string) ($job['name'] ?? $job['title'] ?? $job['jobName'] ?? ''));
            $applyUrl = trim((string) ($job['applyUrl'] ?? $job['jobUrl'] ?? $job['ref'] ?? ''));
            if ($applyUrl !== '' && !filter_var($applyUrl, FILTER_VALIDATE_URL)) {
                $applyUrl = 'https://jobs.smartrecruiters.com/' . ltrim((string) ($job['ref'] ?? $applyUrl), '/');
            }
            if ($title === '' || $applyUrl === '') {
                continue;
            }

            $location = '';
            if (isset($job['location']) && is_array($job['location'])) {
                $location = trim((string) ($job['location']['city'] ?? $job['location']['country'] ?? $job['location']['name'] ?? ''));
            } else {
                $location = trim((string) ($job['location'] ?? $job['city'] ?? ''));
            }

            $department = '';
            if (isset($job['department']) && is_array($job['department'])) {
                $department = trim((string) ($job['department']['label'] ?? $job['department']['name'] ?? ''));
            } else {
                $department = trim((string) ($job['department'] ?? ''));
            }

            $jobs[] = [
                'id' => (string) ($job['ref'] ?? $applyUrl),
                'title' => $title,
                'location' => $location !== '' ? $location : 'Not specified',
                'department' => $department,
                'posted_at' => trim((string) ($job['releasedDate'] ?? $job['createdDate'] ?? $job['datePosted'] ?? '')),
                'apply_url' => $applyUrl,
                'platform' => 'smartrecruiters',
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

        if ($platform === 'smartrecruiters') {
            return $this->fetchSmartRecruiters($slug, $limit);
        }

        if ($platform === 'workday' || $platform === 'successfactors' || $platform === 'icims' || $platform === 'taleo') {
            return [];
        }

        if ($platform === 'tcs') {
            return $this->fetchTcsJobs($slug, $limit);
        }

        return [];
    }

    /**
     * Fetch jobs from TCS Careers platform.
     */
    private function fetchTcsJobs(string $slug, int $limit): array
    {
        $searchUrl = 'https://careers.tcs.com/DSGEP/global/careershome/jobsearch';
        $html = $this->getHtml($searchUrl);

        if (empty($html)) {
            return [];
        }

        // Fallback to AI extraction if no static jobs
        $parser = new TargetCompanyCareerAiParser();
        return $parser->extractJobsFromPage('TCS', $searchUrl, $html, $limit);
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
