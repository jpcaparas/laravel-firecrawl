<?php

namespace JPCaparas\LaravelFirecrawl\Services;

use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\Exceptions\FirecrawlApiException;

class SearchService
{
    protected FirecrawlClient $client;

    public function __construct(FirecrawlClient $client)
    {
        $this->client = $client;
    }

    /**
     * Perform a web search for the given query.
     *
     * @param  string  $query  The search query
     * @param  array  $options  Additional options for the search
     * @return array Response containing search results
     *
     * @throws FirecrawlApiException
     */
    public function search(string $query, array $options = []): array
    {
        $payload = array_merge(['query' => $query], $options);

        return $this->client->post('search', $payload);
    }
}
