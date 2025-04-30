<?php

namespace JPCaparas\LaravelFirecrawl\Services;

use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\Exceptions\FirecrawlApiException;

class ExtractService
{
    protected FirecrawlClient $client;

    public function __construct(FirecrawlClient $client)
    {
        $this->client = $client;
    }

    /**
     * Extract data from URLs using AI.
     *
     * @param  array  $urls  Array of URLs to extract data from
     * @param  string  $prompt  The prompt to guide the extraction
     * @param  array  $schema  JSON Schema for structuring the extracted data
     * @param  bool  $enableWebSearch  Whether to enable web search (optional)
     * @return array Response data containing job ID and status
     *
     * @throws FirecrawlApiException
     */
    public function extract(
        array $urls,
        string $prompt,
        array $schema,
        bool $enableWebSearch = false
    ): array {
        $payload = [
            'urls' => $urls,
            'prompt' => $prompt,
            'schema' => $schema,
        ];

        if ($enableWebSearch) {
            $payload['enableWebSearch'] = true;
        }

        return $this->client->post('extract', $payload);
    }

    /**
     * Get the status and results of an extraction job.
     *
     * @param  string  $extractId  The ID of the extraction job
     * @return array The extraction job data including results if completed
     *
     * @throws FirecrawlApiException
     */
    public function getExtractionStatus(string $extractId): array
    {
        return $this->client->get("extract/{$extractId}");
    }
}
