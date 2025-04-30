<?php

namespace JPCaparas\LaravelFirecrawl;

use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\Services\CrawlService;
use JPCaparas\LaravelFirecrawl\Services\ExtractService;
use JPCaparas\LaravelFirecrawl\Services\MapService;
use JPCaparas\LaravelFirecrawl\Services\ScrapeService;
use JPCaparas\LaravelFirecrawl\Services\SearchService;

class LaravelFirecrawl
{
    protected FirecrawlClient $client;

    protected CrawlService $crawlService;

    protected ExtractService $extractService;

    protected MapService $mapService;

    protected ScrapeService $scrapeService;

    protected SearchService $searchService;

    public function __construct(?string $apiKey = null)
    {
        $this->client = new FirecrawlClient($apiKey);
        $this->initializeServices();
    }

    /**
     * Initialize all service classes
     */
    protected function initializeServices(): void
    {
        $this->crawlService = new CrawlService($this->client);
        $this->extractService = new ExtractService($this->client);
        $this->mapService = new MapService($this->client);
        $this->scrapeService = new ScrapeService($this->client);
        $this->searchService = new SearchService($this->client);
    }

    /**
     * Get the crawl service instance
     */
    public function crawl(): CrawlService
    {
        return $this->crawlService;
    }

    /**
     * Get the extract service instance
     */
    public function extract(): ExtractService
    {
        return $this->extractService;
    }

    /**
     * Get the map service instance
     */
    public function map(): MapService
    {
        return $this->mapService;
    }

    /**
     * Get the scrape service instance
     */
    public function scrape(): ScrapeService
    {
        return $this->scrapeService;
    }

    /**
     * Get the search service instance
     */
    public function search(): SearchService
    {
        return $this->searchService;
    }
}
