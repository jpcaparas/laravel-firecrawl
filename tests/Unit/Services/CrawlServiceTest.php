<?php

namespace JPCaparas\LaravelFirecrawl\Tests\Unit\Services;

use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\Services\CrawlService;
use JPCaparas\LaravelFirecrawl\Tests\TestCase;
use Mockery;

class CrawlServiceTest extends TestCase
{
    protected FirecrawlClient $client;

    protected CrawlService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(FirecrawlClient::class);
        $this->service = new CrawlService($this->client);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_start_a_crawl_operation()
    {
        $url = 'https://example.com';
        $options = [
            'maxDepth' => 2,
            'allowExternalLinks' => false,
        ];

        $expectedResponse = [
            'id' => 'test-crawl-id',
            'status' => 'pending',
        ];

        $this->client->shouldReceive('post')
            ->once()
            ->with('crawl', [
                'url' => $url,
                'maxDepth' => 2,
                'allowExternalLinks' => false,
            ])
            ->andReturn($expectedResponse);

        $response = $this->service->crawl($url, $options);

        $this->assertEquals($expectedResponse, $response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_get_crawl_status()
    {
        $crawlId = 'test-crawl-id';

        $expectedResponse = [
            'id' => $crawlId,
            'status' => 'completed',
            'results' => [
                'pages' => [
                    [
                        'url' => 'https://example.com',
                        'title' => 'Example Website',
                        'markdown' => '# Example Website\n\nThis is an example.',
                    ],
                ],
            ],
        ];

        $this->client->shouldReceive('get')
            ->once()
            ->with("crawl/{$crawlId}")
            ->andReturn($expectedResponse);

        $response = $this->service->getCrawlStatus($crawlId);

        $this->assertEquals($expectedResponse, $response);
    }
}
