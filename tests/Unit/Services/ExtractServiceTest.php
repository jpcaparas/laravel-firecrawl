<?php

namespace JPCaparas\LaravelFirecrawl\Tests\Unit\Services;

use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\Services\ExtractService;
use JPCaparas\LaravelFirecrawl\Tests\TestCase;
use Mockery;

class ExtractServiceTest extends TestCase
{
    protected FirecrawlClient $client;

    protected ExtractService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(FirecrawlClient::class);
        $this->service = new ExtractService($this->client);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_extract_data_from_urls()
    {
        $urls = ['https://example.com'];
        $prompt = 'Extract company information';
        $schema = [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
            ],
        ];

        $expectedResponse = [
            'id' => 'test-extract-id',
            'status' => 'pending',
        ];

        $this->client->shouldReceive('post')
            ->once()
            ->with('extract', [
                'urls' => $urls,
                'prompt' => $prompt,
                'schema' => $schema,
            ])
            ->andReturn($expectedResponse);

        $response = $this->service->extract($urls, $prompt, $schema);

        $this->assertEquals($expectedResponse, $response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_get_extraction_status()
    {
        $extractId = 'test-extract-id';

        $expectedResponse = [
            'id' => $extractId,
            'status' => 'completed',
            'results' => [
                'data' => [
                    'name' => 'Example Company',
                ],
            ],
        ];

        $this->client->shouldReceive('get')
            ->once()
            ->with("extract/{$extractId}")
            ->andReturn($expectedResponse);

        $response = $this->service->getExtractionStatus($extractId);

        $this->assertEquals($expectedResponse, $response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_web_search_when_enabled()
    {
        $urls = ['https://example.com'];
        $prompt = 'Extract company information';
        $schema = [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
            ],
        ];
        $enableWebSearch = true;

        $expectedResponse = [
            'id' => 'test-extract-id',
            'status' => 'pending',
        ];

        $this->client->shouldReceive('post')
            ->once()
            ->with('extract', [
                'urls' => $urls,
                'prompt' => $prompt,
                'schema' => $schema,
                'enableWebSearch' => true,
            ])
            ->andReturn($expectedResponse);

        $response = $this->service->extract($urls, $prompt, $schema, $enableWebSearch);

        $this->assertEquals($expectedResponse, $response);
    }
}
