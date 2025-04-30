<?php

namespace JPCaparas\LaravelFirecrawl\Services;

use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\Exceptions\FirecrawlApiException;

class ScrapeService
{
    protected FirecrawlClient $client;

    public function __construct(FirecrawlClient $client)
    {
        $this->client = $client;
    }

    /**
     * Scrape a single URL and get content in various formats.
     *
     * @param  string  $url  URL to scrape
     * @param  array  $options  Additional options for the scrape operation
     * @return array Response containing the scrape results
     *
     * @throws FirecrawlApiException
     */
    public function scrape(string $url, array $options = []): array
    {
        $payload = array_merge(['url' => $url], $options);

        return $this->client->post('scrape', $payload);
    }
}
