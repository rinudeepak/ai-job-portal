<?php

namespace App\Libraries;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class JobAggregator
{
    private $indeedPublisherId;
    private $client;
    private $cache;

    public function __construct()
    {
        $this->indeedPublisherId = env('INDEED_PUBLISHER_ID');
        $this->client = new Client();
        $this->cache = service('cache');
    }

    /**
     * Search jobs by company name from Indeed API
     */
    public function fetchJobsByCompany(string $companyName, int $limit = 25): array
    {
        $cacheKey = "jobs_company_" . strtolower(str_replace(' ', '_', $companyName));
        
        // Check cache first
        if ($cached = $this->cache->get($cacheKey)) {
            return $cached;
        }

        try {
            $jobs = $this->fetchFromIndeed($companyName, $limit);
            
            // Cache for 6 hours
            $this->cache->save($cacheKey, $jobs, 21600);
            
            log_message('info', "Fetched " . count($jobs) . " jobs for {$companyName}");
            
            return $jobs;
        } catch (\Exception $e) {
            log_message('error', 'Job Aggregator Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch from Indeed API
     */
    private function fetchFromIndeed(string $companyName, int $limit = 25): array
    {
        $url = 'https://api.indeed.com/ads/apisearch';
        
        $params = [
            'publisher' => $this->indeedPublisherId,
            'q' => '',
            'co' => $companyName,
            'format' => 'json',
            'limit' => $limit,
            'sort' => 'date',
            'radius' => 25,
            'start' => 0,
            'userip' => $this->getUserIp(),
            'useragent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0'
        ];

        $response = $this->client->get($url, ['query' => $params]);
        $data = json_decode($response->getBody(), true);

        if (!isset($data['results'])) {
            return [];
        }

        return $this->formatIndeedJobs($data['results'], $companyName);
    }

    /**
     * Format Indeed API response
     */
    private function formatIndeedJobs(array $results, string $companyName): array
    {
        $formatted = [];

        foreach ($results as $job) {
            $formatted[] = [
                'id' => $job['jobkey'] ?? uniqid(),
                'title' => $job['jobtitle'] ?? 'N/A',
                'company' => $job['company'] ?? $companyName,
                'location' => $job['formattedLocation'] ?? 'N/A',
                'summary' => $job['snippet'] ?? '',
                'url' => $job['url'] ?? '',
                'posted_date' => $job['date'] ?? date('Y-m-d'),
                'salary' => $job['salary'] ?? 'Not specified',
                'job_type' => $job['jobtype'] ?? 'Full-time',
                'source' => 'indeed',
                'external_source' => 'Indeed',
                'is_external' => 1
            ];
        }

        return $formatted;
    }

    /**
     * Get user IP for Indeed API
     */
    private function getUserIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        }
        return trim($ip);
    }

    /**
     * Search jobs by keyword and company
     */
    public function searchJobs(string $keyword, string $companyName = '', int $limit = 25): array
    {
        $cacheKey = "jobs_search_" . md5($keyword . $companyName);
        
        if ($cached = $this->cache->get($cacheKey)) {
            return $cached;
        }

        try {
            $url = 'https://api.indeed.com/ads/apisearch';
            
            $params = [
                'publisher' => $this->indeedPublisherId,
                'q' => $keyword,
                'co' => $companyName,
                'format' => 'json',
                'limit' => $limit,
                'sort' => 'relevance',
                'userip' => $this->getUserIp(),
                'useragent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0'
            ];

            $response = $this->client->get($url, ['query' => $params]);
            $data = json_decode($response->getBody(), true);

            $jobs = isset($data['results']) ? $this->formatIndeedJobs($data['results'], $companyName) : [];
            
            $this->cache->save($cacheKey, $jobs, 21600);
            
            return $jobs;
        } catch (\Exception $e) {
            log_message('error', 'Job Search Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get job details
     */
    public function getJobDetails(string $jobKey): array
    {
        $cacheKey = "job_detail_" . $jobKey;
        
        if ($cached = $this->cache->get($cacheKey)) {
            return $cached;
        }

        try {
            $url = 'https://api.indeed.com/ads/apisearch';
            
            $params = [
                'publisher' => $this->indeedPublisherId,
                'jobkeys' => $jobKey,
                'format' => 'json',
                'userip' => $this->getUserIp(),
                'useragent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0'
            ];

            $response = $this->client->get($url, ['query' => $params]);
            $data = json_decode($response->getBody(), true);

            $job = isset($data['results'][0]) ? $data['results'][0] : [];
            
            $this->cache->save($cacheKey, $job, 86400);
            
            return $job;
        } catch (\Exception $e) {
            log_message('error', 'Job Details Error: ' . $e->getMessage());
            return [];
        }
    }
}
