<?php

namespace JPCaparas\LaravelFirecrawl\Tests\Unit\Services;

use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\Services\ScrapeService;
use JPCaparas\LaravelFirecrawl\Tests\TestCase;
use Mockery;

class ScrapeServiceTest extends TestCase
{
    protected FirecrawlClient $client;

    protected ScrapeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(FirecrawlClient::class);
        $this->service = new ScrapeService($this->client);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_scrape_a_url()
    {
        $url = 'https://example.com';
        $options = [
            'outputFormat' => 'markdown',
            'includeScreenshot' => true,
        ];

        $expectedResponse = [
            'url' => $url,
            'title' => 'Example Domain',
            'markdown' => '# Example Domain\n\nThis domain is for use in illustrative examples in documents.',
            'screenshot' => 'base64-encoded-image',
        ];

        $this->client->shouldReceive('post')
            ->once()
            ->with('scrape', [
                'url' => $url,
                'outputFormat' => 'markdown',
                'includeScreenshot' => true,
            ])
            ->andReturn($expectedResponse);

        $response = $this->service->scrape($url, $options);

        $this->assertEquals($expectedResponse, $response);
    }
}
