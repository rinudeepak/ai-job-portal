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
            'model' => 'gpt-4o-mini',
            'messages' => [[
                'role' => 'system',
                'content' => $systemContent
            ], [
                'role' => 'user',
                'content' => $prompt,
            ]],
            'temperature' => 0.0,
            'max_tokens' => 16000,
            'stream' => false,
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . trim($apiKey),
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 90,
        ]);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
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

        $firstBrace = strpos($content, '{');
        $firstBracket = strpos($content, '[');
        $start = false;

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
        $lastBrace = strrpos($content, '}');
        $end = max($lastBracket ?: 0, $lastBrace ?: 0);

        if ($end === 0 || $end <= $start) {
            $json = $this->findJsonSubstring($content);
            if ($json === null) {
                log_message('error', 'No JSON end token found in OpenAI response. Response: ' . substr($content, 0, 500));
                return '{}';
            }
            return $json;
        }

        $json = substr($content, $start, $end - $start + 1);
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
            $decoded = json_decode($candidate, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $candidate;
            }
        }

        return null;
    }
}
