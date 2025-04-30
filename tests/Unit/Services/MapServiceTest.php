<?php

namespace JPCaparas\LaravelFirecrawl\Tests\Unit\Services;

use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\Services\MapService;
use JPCaparas\LaravelFirecrawl\Tests\TestCase;
use Mockery;

class MapServiceTest extends TestCase
{
    protected FirecrawlClient $client;

    protected MapService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(FirecrawlClient::class);
        $this->service = new MapService($this->client);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_map_a_website()
    {
        $url = 'https://example.com';
        $options = [
            'maxDepth' => 3,
            'allowExternalLinks' => false,
        ];

        $expectedResponse = [
            'url' => $url,
            'urls' => [
                'https://example.com',
                'https://example.com/about',
                'https://example.com/contact',
            ],
        ];

        $this->client->shouldReceive('post')
            ->once()
            ->with('map', [
                'url' => $url,
                'maxDepth' => 3,
                'allowExternalLinks' => false,
            ])
            ->andReturn($expectedResponse);

        $response = $this->service->map($url, $options);

        $this->assertEquals($expectedResponse, $response);
    }
}
