<?php
namespace TrinoClient;

use TrinoClient\Exceptions\TrinoException;

class Client
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Execute query and return all results (sync)
     */
    public function query(string $sql): array
    {
        $results = [];
        foreach ($this->stream($sql) as $row) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Stream query results (memory-friendly)
     */
    public function stream(string $sql): \Generator
    {
        $query = new Query($sql);
        $query->nextUri = $this->postStatement($sql);

        while ($query->nextUri) {
            $response = $this->getJson($query->nextUri);

            if (isset($response['error'])) {
                throw new TrinoException($response['error']['message'] ?? 'Unknown Trino error');
            }

            if (!empty($response['data'])) {
                foreach ($response['data'] as $row) {
                    yield $row;
                }
            }

            $query->nextUri = $response['nextUri'] ?? null;
        }
    }

    /**
     * Start async query and return nextUri
     */
    public function startAsyncQuery(string $sql): string
    {
        return $this->postStatement($sql);
    }

    /**
     * Poll async query until finished (streaming)
     */
    public function pollAsync(string $nextUri): \Generator
    {
        $uri = $nextUri;
        while ($uri) {
            $response = $this->getJson($uri);

            if (isset($response['error'])) {
                throw new TrinoException($response['error']['message'] ?? 'Unknown Trino error');
            }

            if (!empty($response['data'])) {
                foreach ($response['data'] as $row) {
                    yield $row;
                }
            }

            $uri = $response['nextUri'] ?? null;
        }
    }

    private function postStatement(string $sql): string
    {
        $ch = curl_init($this->config->url . '/v1/statement');
        $headers = $this->buildHeaders();

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $sql,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->config->timeout,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new TrinoException('cURL error: ' . $error);
        }

        $data = json_decode($response, true);
        if (!isset($data['nextUri'])) {
            throw new TrinoException('Invalid response from Trino: ' . $response);
        }

        return $data['nextUri'];
    }

    private function getJson(string $url): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->config->timeout,
            CURLOPT_HTTPHEADER => $this->buildHeaders(),
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new TrinoException('cURL error: ' . $error);
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            throw new TrinoException('Invalid JSON response from Trino');
        }

        return $data;
    }

    private function buildHeaders(): array
    {
        $headers = [
            'X-Trino-User: ' . $this->config->user,
        ];

        if ($this->config->catalog) {
            $headers[] = 'X-Trino-Catalog: ' . $this->config->catalog;
        }
        if ($this->config->schema) {
            $headers[] = 'X-Trino-Schema: ' . $this->config->schema;
        }

        if ($this->config->authType && $this->config->authToken) {
            switch ($this->config->authType) {
                case 'basic':
                    $headers[] = 'Authorization: Basic ' . base64_encode($this->config->authToken);
                    break;
                case 'bearer':
                    $headers[] = 'Authorization: Bearer ' . $this->config->authToken;
                    break;
            }
        }

        return $headers;
    }
}

