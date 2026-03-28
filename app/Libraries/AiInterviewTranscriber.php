<?php

namespace App\Libraries;

class AiInterviewTranscriber
{
    private string $apiKey;
    private string $apiUrl = 'https://api.openai.com/v1/audio/transcriptions';
    private string $model;

    public function __construct()
    {
        $this->apiKey = (string) (getenv('OPENAI_API_KEY') ?: '');
        $this->model = (string) (getenv('OPENAI_TRANSCRIBE_MODEL') ?: 'whisper-1');
    }

    public function transcribeFile(string $absolutePath): ?string
    {
        if ($this->apiKey === '' || !is_file($absolutePath)) {
            return null;
        }

        $mime = $this->detectMimeType($absolutePath);
        $file = new \CURLFile($absolutePath, $mime, basename($absolutePath));
        $post = [
            'model' => $this->model,
            'file' => $file,
            'response_format' => 'json',
            'language' => 'en',
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . trim($this->apiKey),
            ],
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_TIMEOUT => 180,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlError || $httpCode !== 200) {
            log_message('warning', 'Interview transcription failed: ' . ($curlError ?: 'HTTP ' . $httpCode));
            return null;
        }

        $payload = json_decode((string) $response, true);
        if (!is_array($payload)) {
            return null;
        }

        $text = trim((string) ($payload['text'] ?? ''));
        return $text !== '' ? $text : null;
    }

    private function detectMimeType(string $path): string
    {
        if (function_exists('mime_content_type')) {
            $type = (string) @mime_content_type($path);
            if ($type !== '') {
                return $type;
            }
        }

        return 'video/webm';
    }
}

