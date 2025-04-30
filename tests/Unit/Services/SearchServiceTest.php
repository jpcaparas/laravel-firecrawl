<?php

namespace JPCaparas\LaravelFirecrawl\Tests\Unit\Services;

use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\Services\SearchService;
use JPCaparas\LaravelFirecrawl\Tests\TestCase;
use Mockery;

class SearchServiceTest extends TestCase
{
    protected FirecrawlClient $client;

    protected SearchService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(FirecrawlClient::class);
        $this->service = new SearchService($this->client);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_perform_a_search()
    {
        $query = 'example search query';
        $options = [
            'limit' => 10,
            'filterDomain' => 'example.com',
        ];

        $expectedResponse = [
            'query' => $query,
            'results' => [
                [
                    'title' => 'Example Search Result',
                    'url' => 'https://example.com/result',
                    'snippet' => 'This is an example search result.',
                ],
            ],
        ];

        $this->client->shouldReceive('post')
            ->once()
            ->with('search', [
                'query' => $query,
                'limit' => 10,
                'filterDomain' => 'example.com',
            ])
            ->andReturn($expectedResponse);

        $response = $this->service->search($query, $options);

        $this->assertEquals($expectedResponse, $response);
    }
}
