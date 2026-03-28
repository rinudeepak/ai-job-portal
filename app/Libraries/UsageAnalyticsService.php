<?php

namespace App\Libraries;

class UsageAnalyticsService
{
    public function captureFirstPageAfterLogin(string $path): void
    {
        try {
            $session = session();
            if (!$session->get('logged_in') || (int) $session->get('login_perf_pending') !== 1) {
                return;
            }

            $loginStartedAtMs = (int) $session->get('login_started_at_ms');
            if ($loginStartedAtMs <= 0) {
                $this->clearLoginPerfSession($session);
                return;
            }

            $nowMs = (int) round(microtime(true) * 1000);
            $durationMs = max(0, $nowMs - $loginStartedAtMs);
            $now = date('Y-m-d H:i:s');
            $loginAt = (string) ($session->get('login_at') ?: $now);

            $db = \Config\Database::connect();
            if (!$db->tableExists('user_login_performance_logs')) {
                $this->clearLoginPerfSession($session);
                return;
            }

            $db->table('user_login_performance_logs')->insert([
                'user_id' => (int) ($session->get('user_id') ?: 0) ?: null,
                'user_email' => (string) ($session->get('user_email') ?: ''),
                'user_role' => (string) ($session->get('role') ?: ''),
                'login_at' => $loginAt,
                'first_page_path' => $path,
                'first_page_loaded_at' => $now,
                'duration_ms' => $durationMs,
            ]);

            $this->clearLoginPerfSession($session);
        } catch (\Throwable $e) {
            log_message('error', 'UsageAnalyticsService login perf capture failed: ' . $e->getMessage());
        }
    }

    public function logOpenAiUsage(array $payload, string $endpoint, ?string $modelOverride = null): void
    {
        try {
            $usage = (array) ($payload['usage'] ?? []);
            $promptTokens = (int) ($usage['prompt_tokens'] ?? 0);
            $completionTokens = (int) ($usage['completion_tokens'] ?? 0);
            $totalTokens = (int) ($usage['total_tokens'] ?? ($promptTokens + $completionTokens));
            if ($totalTokens <= 0) {
                return;
            }

            $model = trim((string) ($modelOverride ?: ($payload['model'] ?? '')));
            $estimatedCost = $this->estimateOpenAiCost($model, $promptTokens, $completionTokens);
            $now = date('Y-m-d H:i:s');

            $userId = null;
            $userEmail = null;
            $userRole = null;
            try {
                $session = session();
                $uid = (int) ($session->get('user_id') ?: 0);
                $userId = $uid > 0 ? $uid : null;
                $userEmail = (string) ($session->get('user_email') ?: '');
                $userRole = (string) ($session->get('role') ?: '');
            } catch (\Throwable $e) {
                // Ignore session resolution in non-http contexts.
            }

            $db = \Config\Database::connect();
            if (!$db->tableExists('admin_api_usage_logs')) {
                return;
            }

            $db->table('admin_api_usage_logs')->insert([
                'user_id' => $userId,
                'user_email' => $userEmail,
                'user_role' => $userRole,
                'provider' => 'openai',
                'endpoint' => $endpoint,
                'model' => $model,
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens' => $totalTokens,
                'estimated_cost_usd' => $estimatedCost,
                'created_at' => $now,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'UsageAnalyticsService API usage capture failed: ' . $e->getMessage());
        }
    }

    private function clearLoginPerfSession($session): void
    {
        $session->remove(['login_perf_pending', 'login_started_at_ms', 'login_at']);
    }

    private function estimateOpenAiCost(string $model, int $promptTokens, int $completionTokens): float
    {
        $modelKey = strtolower(trim($model));
        $defaultInput = (float) (env('analytics.openaiDefaultInputCostPer1k') ?? 0.00015);
        $defaultOutput = (float) (env('analytics.openaiDefaultOutputCostPer1k') ?? 0.00060);

        $inputRate = $defaultInput;
        $outputRate = $defaultOutput;

        if (str_contains($modelKey, 'gpt-4o-mini')) {
            $inputRate = (float) (env('analytics.openaiGpt4oMiniInputCostPer1k') ?? $defaultInput);
            $outputRate = (float) (env('analytics.openaiGpt4oMiniOutputCostPer1k') ?? $defaultOutput);
        }

        $promptCost = ($promptTokens / 1000) * max(0, $inputRate);
        $completionCost = ($completionTokens / 1000) * max(0, $outputRate);

        return round($promptCost + $completionCost, 6);
    }
}
