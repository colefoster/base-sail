<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiTesterService
{
    private const BLOCKED_HOSTNAMES = [
        'localhost',
        '127.0.0.1',
        '0.0.0.0',
        '::1',
    ];

    /**
     * Test an API request.
     */
    public function testRequest(string $url): array
    {
        $this->validateUrl($url);
        $this->checkSsrfVulnerability($url);

        try {
            $startTime = microtime(true);
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->get($url);
            $endTime = microtime(true);

            $statusCode = $response->status();
            $body = $response->json() ?? $response->body();
            $headers = $response->headers();
            $contentLength = strlen($response->body());

            return [
                'statusCode' => $statusCode,
                'body' => $body,
                'headers' => $this->formatHeaders($headers),
                'metadata' => [
                    'statusCode' => $statusCode,
                    'contentLength' => $this->formatBytes($contentLength),
                    'contentType' => $response->header('Content-Type') ?? 'application/json',
                    'executionTime' => round(($endTime - $startTime) * 1000, 2),
                ],
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception("Connection failed: {$e->getMessage()}");
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return [
                'statusCode' => $e->response->status(),
                'body' => $e->response->json() ?? $e->response->body(),
                'headers' => $this->formatHeaders($e->response->headers()),
                'metadata' => [
                    'statusCode' => $e->response->status(),
                    'contentLength' => $this->formatBytes(strlen($e->response->body())),
                    'contentType' => $e->response->header('Content-Type') ?? 'application/json',
                    'executionTime' => 0,
                    'error' => true,
                ],
            ];
        } catch (\Exception $e) {
            throw new \Exception("Request error: {$e->getMessage()}");
        }
    }

    /**
     * Validate the URL format.
     */
    private function validateUrl(string $url): void
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception('Invalid URL format. Please provide a valid HTTP/HTTPS URL.');
        }

        if (! preg_match('/^https?:\/\//i', $url)) {
            throw new \Exception('Only HTTP and HTTPS protocols are supported.');
        }

        if (strlen($url) > 2048) {
            throw new \Exception('URL is too long. Maximum 2048 characters allowed.');
        }
    }

    /**
     * Check for SSRF vulnerability attempts.
     */
    private function checkSsrfVulnerability(string $url): void
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';

        if (in_array(strtolower($host), self::BLOCKED_HOSTNAMES)) {
            throw new \Exception("Access to '{$host}' is not allowed for security reasons.");
        }

        $ip = @gethostbyname($host);

        if ($ip === $host) {
            throw new \Exception("Unable to resolve hostname: {$host}");
        }

        if ($this->isPrivateIp($ip)) {
            throw new \Exception('Access to private IP addresses is blocked for security reasons.');
        }
    }

    /**
     * Check if IP is in private ranges.
     */
    private function isPrivateIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    /**
     * Format headers for display.
     */
    private function formatHeaders(array $headers): array
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[$key] = is_array($value) ? implode(', ', $value) : $value;
        }

        return $formatted;
    }

    /**
     * Format bytes to human-readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2).' '.$units[$pow];
    }
}
