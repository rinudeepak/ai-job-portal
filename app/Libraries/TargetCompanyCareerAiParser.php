<?php

namespace App\Libraries;

class TargetCompanyCareerAiParser
{
    public function guessCareerPageUrl(string $companyName): ?string
    {
        $companyName = trim($companyName);
        if ($companyName === '') {
            return null;
        }

        $prompt = "You are a job search assistant. " .
            "Given a company name, return the most likely official careers page URL for that company. " .
            "If you are not sure, return an empty string. " .
            "Respond with a single URL only, without any explanation.\n\n" .
            "Company: {$companyName}\n";

        $response = $this->callOpenAI($prompt, false);
        $url = trim($response);

        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return $url;
    }

    public function extractJobsFromPage(string $companyName, string $pageUrl, string $pageHtml, int $limit = 10): array
    {
        $cleanHtml = $this->cleanHtmlForAi($pageHtml);
        $prompt = $this->buildExtractionPrompt($companyName, $pageUrl, $cleanHtml, $limit);
        $response = $this->callOpenAI($prompt);

        $jobs = json_decode($response, true);
        if (!is_array($jobs)) {
            log_message('error', 'AI job extraction failed to decode JSON from ' . $pageUrl);
            return [];
        }

        $validJobs = array_values(array_filter(array_slice($jobs, 0, $limit), [$this, 'isValidJob']));
        log_message('info', 'AI extracted ' . count($validJobs) . ' valid jobs from ' . $pageUrl);
        return $validJobs;
    }

    public function extractCompanyInfoFromPage(string $companyName, string $pageUrl, string $pageHtml): array
    {
        $cleanHtml = $this->cleanHtmlForAi($pageHtml);
        $prompt = $this->buildCompanyInfoPrompt($companyName, $pageUrl, $cleanHtml);
        $response = $this->callOpenAI($prompt);

        $info = json_decode($response, true);
        if (!is_array($info)) {
            log_message('error', 'AI company info extraction failed to decode JSON from ' . $pageUrl);
            return [];
        }

        $normalized = $this->normalizeCompanyInfo($info);
        $normalized = array_merge($normalized, $this->extractCompanyLinksFromHtml($pageHtml));

        return $normalized;
    }

    private function buildCompanyInfoPrompt(string $companyName, string $pageUrl, string $pageHtml): string
    {
        return "You are a company details extraction assistant. Extract the official company details from the page and return valid JSON only with these exact fields:\n" .
            "- name: official company name\n" .
            "- short_description: one-line tagline or summary (max 160 chars)\n" .
            "- what_we_do: 2-4 sentence paragraph describing what the company does\n" .
            "- hq: headquarters city and country (e.g. Bangalore, India)\n" .
            "- industry: primary industry (e.g. Information Technology, Finance, Healthcare)\n" .
            "- size: employee count range (e.g. 1000+, 10000+, 50-200)\n" .
            "- founded_year: 4-digit year the company was founded (e.g. 1994)\n" .
            "- website: official website URL\n" .
            "- career_page: careers/jobs page URL\n" .
            "- linkedin: LinkedIn company page URL\n" .
            "- twitter: Twitter/X page URL\n" .
            "- facebook: Facebook page URL\n" .
            "- instagram: Instagram page URL\n" .
            "- youtube: YouTube channel URL\n" .
            "If a field is not available on the page, return an empty string for it. Do not add any extra text or markdown.\n\n" .
            "Company: {$companyName}\n" .
            "Page URL: {$pageUrl}\n" .
            "HTML:\n{$pageHtml}\n";
    }

    private function normalizeCompanyInfo(array $info): array
    {
        return [
            'name'              => trim((string) ($info['name'] ?? '')),
            'short_description' => trim((string) ($info['short_description'] ?? $info['description'] ?? '')),
            'what_we_do'        => trim((string) ($info['what_we_do'] ?? '')),
            'hq'                => trim((string) ($info['hq'] ?? $info['location'] ?? '')),
            'industry'          => trim((string) ($info['industry'] ?? '')),
            'size'              => trim((string) ($info['size'] ?? '')),
            'founded_year'      => trim((string) ($info['founded_year'] ?? '')),
            'website'           => trim((string) ($info['website'] ?? '')),
            'career_page'       => trim((string) ($info['career_page'] ?? '')),
            'linkedin'          => trim((string) ($info['linkedin'] ?? '')),
            'twitter'           => trim((string) ($info['twitter'] ?? '')),
            'facebook'          => trim((string) ($info['facebook'] ?? '')),
            'instagram'         => trim((string) ($info['instagram'] ?? '')),
            'youtube'           => trim((string) ($info['youtube'] ?? '')),
        ];
    }

    private function extractCompanyLinksFromHtml(string $html): array
    {
        $socials = [
            'linkedin'  => '',
            'twitter'   => '',
            'facebook'  => '',
            'instagram' => '',
            'youtube'   => '',
        ];

        if (trim($html) === '') {
            return $socials;
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//a[@href]') as $node) {
            $href = trim((string) $node->getAttribute('href'));
            if ($href === '') {
                continue;
            }

            $hrefLower = strtolower($href);
            if ($socials['linkedin'] === '' && str_contains($hrefLower, 'linkedin.com')) {
                $socials['linkedin'] = $href;
            } elseif ($socials['twitter'] === '' && (str_contains($hrefLower, 'twitter.com') || str_contains($hrefLower, 'x.com'))) {
                $socials['twitter'] = $href;
            } elseif ($socials['facebook'] === '' && str_contains($hrefLower, 'facebook.com')) {
                $socials['facebook'] = $href;
            } elseif ($socials['instagram'] === '' && str_contains($hrefLower, 'instagram.com')) {
                $socials['instagram'] = $href;
            } elseif ($socials['youtube'] === '' && (str_contains($hrefLower, 'youtube.com') || str_contains($hrefLower, 'youtu.be'))) {
                $socials['youtube'] = $href;
            }
        }

        return $socials;
    }

    private function buildExtractionPrompt(string $companyName, string $pageUrl, string $pageHtml, int $limit): string
    {
        return "You are a jobs extraction assistant. " .
            "Extract up to {$limit} current open job postings from the official company careers page. " .
            "Return only valid JSON in the following form:\n" .
            "[\n  {\n    \"title\": \"...\",\n    \"location\": \"...\",\n    \"department\": \"...\",\n    \"posted_at\": \"...\",\n    \"apply_url\": \"...\"\n  }\n]\n" .
            "If the page contains no jobs, return an empty JSON array: []\n" .
            "Do not add any markdown, comments, or extra text.\n\n" .
            "Company: {$companyName}\n" .
            "URL: {$pageUrl}\n" .
            "HTML:\n{$pageHtml}\n";
    }

    private function cleanHtmlForAi(string $html): string
    {
        $html = preg_replace('#<script[^>]*>.*?</script>#si', '', $html);
        $html = preg_replace('#<style[^>]*>.*?</style>#si', '', $html);
        $html = preg_replace('#<!--.*?-->#s', '', $html);
        $html = trim($html);

        if (mb_strlen($html, 'UTF-8') > 120000) {
            $html = mb_substr($html, 0, 120000, 'UTF-8');
        }

        return $html;
    }

    private function isValidJob(array $job): bool
    {
        return isset($job['title'], $job['apply_url'])
            && is_string($job['title'])
            && trim($job['title']) !== ''
            && is_string($job['apply_url'])
            && filter_var(trim($job['apply_url']), FILTER_VALIDATE_URL);
    }

    private function callOpenAI(string $prompt, bool $expectJson = true): string
    {
        $apiKey = $this->getApiKey();
        if ($apiKey === '') {
            log_message('error', 'OpenAI API key missing from .env');
            return $expectJson ? '{}' : '';
        }

        $systemContent = $expectJson
            ? 'You are a precise data extraction assistant. Return valid JSON only.'
            : 'You are a precise assistant. Return only the requested URL or single value, without extra explanation.';

        $data = [
            'model'    => 'gpt-4o-mini',
            'messages' => [[
                'role'    => 'system',
                'content' => $systemContent,
            ], [
                'role'    => 'user',
                'content' => $prompt,
            ]],
            'temperature' => 0.0,
            'max_tokens'  => 16000,
            'stream'      => false,
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . trim($apiKey),
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT    => 90,
        ]);

        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || !empty($curlError)) {
            log_message('error', 'OpenAI cURL error: ' . $curlError);
            return '{}';
        }

        if ($httpCode !== 200) {
            log_message('error', 'OpenAI API Error: HTTP ' . $httpCode . ' - ' . substr($response, 0, 500));
            return $expectJson ? '{}' : '';
        }

        $data = json_decode($response, true);
        if (!isset($data['choices'][0]['message']['content'])) {
            log_message('error', 'OpenAI response missing content. Response: ' . substr($response, 0, 500));
            return $expectJson ? '{}' : '';
        }

        $content = (string) $data['choices'][0]['message']['content'];
        return $expectJson ? $this->extractJson($content) : trim($content);
    }

    private function getApiKey(): string
    {
        $apiKey = getenv('OPENAI_API_KEY');

        if (empty($apiKey) && function_exists('env')) {
            $apiKey = env('OPENAI_API_KEY');
        }

        if (empty($apiKey)) {
            $apiKey = $_ENV['OPENAI_API_KEY'] ?? $_SERVER['OPENAI_API_KEY'] ?? '';
        }

        if (empty($apiKey)) {
            $apiKey = $this->readEnvFileKey('OPENAI_API_KEY');
        }

        return trim((string) $apiKey);
    }

    public function hasApiKey(): bool
    {
        return $this->getApiKey() !== '';
    }

    private function readEnvFileKey(string $key): string
    {
        $envPath = ROOTPATH . '.env';
        if (!is_file($envPath)) {
            return '';
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$name, $value] = explode('=', $line, 2);
                if (trim($name) === $key) {
                    return trim($value);
                }
            }
        }

        return '';
    }

    private function extractJson(string $content): string
    {
        $content = preg_replace('/```(?:json)?\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        $content = trim($content);

        $firstBrace   = strpos($content, '{');
        $firstBracket = strpos($content, '[');
        $start        = false;

        if ($firstBracket !== false && ($firstBrace === false || $firstBracket < $firstBrace)) {
            $start = $firstBracket;
        } elseif ($firstBrace !== false) {
            $start = $firstBrace;
        }

        if ($start === false) {
            $json = $this->findJsonSubstring($content);
            if ($json === null) {
                log_message('error', 'No JSON start token found in OpenAI response. Response: ' . substr($content, 0, 500));
                return '{}';
            }
            return $json;
        }

        $lastBracket = strrpos($content, ']');
        $lastBrace   = strrpos($content, '}');
        $end         = max($lastBracket ?: 0, $lastBrace ?: 0);

        if ($end === 0 || $end <= $start) {
            $json = $this->findJsonSubstring($content);
            if ($json === null) {
                log_message('error', 'No JSON end token found in OpenAI response. Response: ' . substr($content, 0, 500));
                return '{}';
            }
            return $json;
        }

        $json    = substr($content, $start, $end - $start + 1);
        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $json = $this->findJsonSubstring($content);
            if ($json === null) {
                log_message('error', 'Extracted JSON invalid: ' . json_last_error_msg() . '. Response: ' . substr($content, 0, 500));
                return '{}';
            }
            return $json;
        }

        return $json;
    }

    private function findJsonSubstring(string $content): ?string
    {
        $pattern = '/(\{(?:[^{}]|(?R))*\}|\[(?:[^\[\]]|(?R))*\])/s';
        if (preg_match($pattern, $content, $matches)) {
            $candidate = trim($matches[0]);
            $decoded   = json_decode($candidate, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $candidate;
            }
        }

        return null;
    }
}
