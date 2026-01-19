<?php

namespace App\Libraries;

class GithubAnalyzer
{
    private $token;

    public function __construct()
    {
        $this->token = getenv('GITHUB_TOKEN');

        if (!$this->token) {
            log_message('error', 'GitHub token missing in .env');
        }
    }

    private function request($url)
    {
        $headers = [
            "User-Agent: JobPortal"
        ];

        if ($this->token) {
            $headers[] = "Authorization: token " . $this->token;
        }

        $opts = [
            "http" => [
                "method" => "GET",
                "header" => $headers
            ]
        ];

        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);

        if (!$response) {
            return [];
        }

        return json_decode($response, true);
    }

    public function fetchRepos($username)
    {
        return $this->request("https://api.github.com/users/$username/repos?per_page=10");
    }

    public function fetchCommitCount($username, $repoName)
    {
        $url = "https://api.github.com/repos/$username/$repoName/commits?per_page=1";

        $this->request($url);

        global $http_response_header;

        if (!isset($http_response_header) || !is_array($http_response_header)) {
            return 0; // API failed
        }

        foreach ($http_response_header as $header) {
            if (strpos($header, 'Link:') !== false && strpos($header, 'last') !== false) {
                preg_match('/page=(\d+)>; rel="last"/', $header, $matches);
                return isset($matches[1]) ? (int) $matches[1] : 1;
            }
        }

        return 1;
    }


    public function fetchLanguages($username, $repoName)
    {
        $langs = $this->request("https://api.github.com/repos/$username/$repoName/languages");
        return array_keys($langs);
    }

    public function analyze($username)
    {
        $repos = $this->fetchRepos($username);

        $repoCount = count($repos);
        $totalCommits = 0;
        $languages = [];

        foreach ($repos as $repo) {
            $repoName = $repo['name'];

            $totalCommits += $this->fetchCommitCount($username, $repoName);
            $languages = array_merge($languages, $this->fetchLanguages($username, $repoName));
        }

        return [
            'repo_count' => $repoCount,
            'commit_count' => $totalCommits,
            'languages' => array_unique($languages),
            'github_score' => $this->generateScore($repoCount, $totalCommits)
        ];
    }

    private function generateScore($repos, $commits)
    {
        $score = min(10, round(($repos * 0.5) + ($commits * 0.05)));
        return $score;
    }
}
