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
        $allRepos = [];
        $page = 1;
        $perPage = 100; // max allowed by GitHub

        do {
            $repos = $this->request(
                "https://api.github.com/users/{$username}/repos?per_page={$perPage}&page={$page}"
            );

            if (empty($repos)) {
                break;
            }

            $allRepos = array_merge($allRepos, $repos);
            $page++;

        } while (count($repos) === $perPage);

        return $allRepos;
    }

    public function fetchCommitCount($username, $repoName)
    {
        $url = "https://api.github.com/repos/{$username}/{$repoName}/commits?per_page=1";

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_USERAGENT => 'JobPortal',
            CURLOPT_HTTPHEADER => [
                "Authorization: token {$this->token}",
                "Accept: application/vnd.github+json"
            ],
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            curl_close($ch);
            return 0;
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);

        curl_close($ch);

        // Extract total commit count from Link header
        if (preg_match('/<[^>]*page=(\d+)[^>]*>; rel="last"/', $headers, $matches)) {
            return (int) $matches[1];
        }

        // Repo has 0 or 1 commit
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
