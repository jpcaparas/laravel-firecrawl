<?php

namespace JPCaparas\LaravelFirecrawl\Tests\Unit;

use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\LaravelFirecrawl;
use JPCaparas\LaravelFirecrawl\Services\CrawlService;
use JPCaparas\LaravelFirecrawl\Services\ExtractService;
use JPCaparas\LaravelFirecrawl\Services\MapService;
use JPCaparas\LaravelFirecrawl\Services\ScrapeService;
use JPCaparas\LaravelFirecrawl\Services\SearchService;
use JPCaparas\LaravelFirecrawl\Tests\TestCase;

class LaravelFirecrawlTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_initializes_all_services_on_construction()
    {
        config(['firecrawl.api_key' => 'test-api-key']);

        $firecrawl = new LaravelFirecrawl;

        $this->assertInstanceOf(CrawlService::class, $firecrawl->crawl());
        $this->assertInstanceOf(ExtractService::class, $firecrawl->extract());
        $this->assertInstanceOf(MapService::class, $firecrawl->map());
        $this->assertInstanceOf(ScrapeService::class, $firecrawl->scrape());
        $this->assertInstanceOf(SearchService::class, $firecrawl->search());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_be_constructed_with_a_custom_api_key()
    {
        $customApiKey = 'custom-test-api-key';

        // We need to use reflection to verify the API key is passed correctly
        $firecrawl = new LaravelFirecrawl($customApiKey);

        $reflection = new \ReflectionClass($firecrawl);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $client = $clientProperty->getValue($firecrawl);

        $this->assertInstanceOf(FirecrawlClient::class, $client);

        // Now verify the API key in the client
        $clientReflection = new \ReflectionClass($client);
        $apiKeyProperty = $clientReflection->getProperty('apiKey');
        $apiKeyProperty->setAccessible(true);
        $apiKey = $apiKeyProperty->getValue($client);

        $this->assertEquals($customApiKey, $apiKey);
    }
}
