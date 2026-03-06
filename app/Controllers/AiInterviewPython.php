<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class AiInterviewPython extends BaseController
{
    /**
     * Template entrypoint to forward interview data to Python service.
     */
    public function startInterview(): ResponseInterface
    {
        // $name = trim((string) $this->request->getPost('name'));
        // $email = trim((string) $this->request->getPost('email'));
        // $resume = $this->request->getFile('resume');

        // $errors = $this->validateInput($name, $email, $resume);
        // if (!empty($errors)) {
        //     return $this->response->setStatusCode(422)->setJSON([
        //         'success' => false,
        //         'errors' => $errors,
        //     ]);
        // }

        // $payload = $this->buildPayload($name, $email, $resume);
        // [$httpCode, $rawBody, $curlError] = $this->callPythonApi($payload);

        // return $this->formatApiResponse($httpCode, $rawBody, $curlError);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'AiInterviewPython controller template is ready',
        ]);
    }

    /**
     * Validate incoming name, email and resume.
     */
    private function validateInput(?string $name, ?string $email, $resume): array
    {
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }

        if (!$resume || !$resume->isValid()) {
            $errors['resume'] = 'Resume file is required';
        }

        return $errors;
    }

    /**
     * Build multipart payload for Python API.
     */
    private function buildPayload(string $name, string $email, $resume): array
    {
        // $file = new \CURLFile(
        //     $resume->getTempName(),
        //     $resume->getClientMimeType(),
        //     $resume->getClientName()
        // );

        // return [
        //     'name' => $name,
        //     'email' => $email,
        //     'resume' => $file,
        // ];

        return [];
    }

    /**
     * Perform cURL call to Python service.
     * Returns: [httpCode, rawBody, curlError]
     */
    private function callPythonApi(array $payload): array
    {
        // $url = (string) env('PY_AI_API_URL');
        // $token = (string) env('PY_AI_API_TOKEN');

        // $ch = curl_init($url);

        // $headers = [
        //     'Accept: application/json',
        //     'Authorization: Bearer ' . $token,
        //     // Do not set Content-Type manually for multipart/form-data.
        // ];

        // curl_setopt_array($ch, [
        //     CURLOPT_POST => true,
        //     CURLOPT_POSTFIELDS => $payload,
        //     CURLOPT_HTTPHEADER => $headers,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_CONNECTTIMEOUT => 10,
        //     CURLOPT_TIMEOUT => 60,
        //     CURLOPT_SSL_VERIFYPEER => true,
        //     CURLOPT_SSL_VERIFYHOST => 2,
        //     CURLOPT_FOLLOWLOCATION => false,
        // ]);

        // $rawBody = curl_exec($ch);
        // $curlError = curl_error($ch);
        // $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // curl_close($ch);

        // return [$httpCode, (string) $rawBody, $curlError ?: null];

        return [200, '{"message":"template"}', null];
    }

    /**
     * Normalize downstream API response into app response format.
     */
    private function formatApiResponse(int $httpCode, string $rawBody, ?string $curlError): ResponseInterface
    {
        if ($curlError) {
            return $this->response->setStatusCode(502)->setJSON([
                'success' => false,
                'message' => 'Python service unreachable',
                'error' => $curlError,
            ]);
        }

        $decoded = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->response->setStatusCode(502)->setJSON([
                'success' => false,
                'message' => 'Invalid response from Python service',
            ]);
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return $this->response->setStatusCode(200)->setJSON([
                'success' => true,
                'data' => $decoded,
            ]);
        }

        return $this->response->setStatusCode($httpCode > 0 ? $httpCode : 500)->setJSON([
            'success' => false,
            'message' => $decoded['message'] ?? 'Python API error',
            'data' => $decoded,
        ]);
    }
}
