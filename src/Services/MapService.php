<?php

namespace JPCaparas\LaravelFirecrawl\Services;

use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\Exceptions\FirecrawlApiException;

class MapService
{
    protected FirecrawlClient $client;

    public function __construct(FirecrawlClient $client)
    {
        $this->client = $client;
    }

    /**
     * Map a website to get all its URLs.
     *
     * @param  string  $url  Base URL to map
     * @param  array  $options  Additional options for the map operation
     * @return array Response containing the map results
     *
     * @throws FirecrawlApiException
     */
    public function map(string $url, array $options = []): array
    {
        $payload = array_merge(['url' => $url], $options);

        return $this->client->post('map', $payload);
    }
}
