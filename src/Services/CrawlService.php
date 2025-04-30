<?php

namespace JPCaparas\LaravelFirecrawl\Services;

use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\Exceptions\FirecrawlApiException;

class CrawlService
{
    protected FirecrawlClient $client;

    public function __construct(FirecrawlClient $client)
    {
        $this->client = $client;
    }

    /**
     * Start a crawl operation on a website.
     *
     * @param  string  $url  URL to crawl
     * @param  array  $options  Additional options for the crawl operation
     * @return array Response containing the crawl job ID and status
     *
     * @throws FirecrawlApiException
     */
    public function crawl(string $url, array $options = []): array
    {
        $payload = array_merge(['url' => $url], $options);

        return $this->client->post('crawl', $payload);
    }

    /**
     * Get the status and results of a crawl job.
     *
     * @param  string  $crawlId  The ID of the crawl job
     * @return array The crawl job data including results if completed
     *
     * @throws FirecrawlApiException
     */
    public function getCrawlStatus(string $crawlId): array
    {
        return $this->client->get("crawl/{$crawlId}");
    }
}
