<?php

namespace JPCaparas\LaravelFirecrawl\Tests\Unit\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JPCaparas\LaravelFirecrawl\Client\FirecrawlClient;
use JPCaparas\LaravelFirecrawl\Exceptions\FirecrawlApiException;
use JPCaparas\LaravelFirecrawl\Exceptions\InvalidConfigurationException;
use JPCaparas\LaravelFirecrawl\Tests\TestCase;
use ReflectionClass;

class FirecrawlClientTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_an_exception_if_api_key_is_not_provided()
    {
        $this->expectException(InvalidConfigurationException::class);

        // Ensure the config is empty
        config(['firecrawl.api_key' => null]);

        $client = new FirecrawlClient;
        $client->get('/test-endpoint');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_make_a_get_request()
    {
        // Mock the Guzzle client
        $mockHandler = new MockHandler([
            new Response(200, [], json_encode(['status' => 'success'])),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $guzzleClient = new Client(['handler' => $handlerStack]);

        // Create client with reflection to inject the mock
        $client = new FirecrawlClient('test-api-key');
        $reflection = new ReflectionClass($client);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($client, $guzzleClient);

        $response = $client->get('/test-endpoint');

        $this->assertEquals(['status' => 'success'], $response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_make_a_post_request()
    {
        // Mock the Guzzle client
        $mockHandler = new MockHandler([
            new Response(200, [], json_encode(['status' => 'success'])),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $guzzleClient = new Client(['handler' => $handlerStack]);

        // Create client with reflection to inject the mock
        $client = new FirecrawlClient('test-api-key');
        $reflection = new ReflectionClass($client);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($client, $guzzleClient);

        $response = $client->post('/test-endpoint', ['data' => 'test']);

        $this->assertEquals(['status' => 'success'], $response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_an_exception_when_the_api_request_fails()
    {
        // Mock a failed Guzzle client response
        $mockHandler = new MockHandler([
            new \GuzzleHttp\Exception\RequestException(
                'Error communicating with server',
                new \GuzzleHttp\Psr7\Request('GET', '/test-endpoint')
            ),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $guzzleClient = new Client(['handler' => $handlerStack]);

        // Create client with reflection to inject the mock
        $client = new FirecrawlClient('test-api-key');
        $reflection = new ReflectionClass($client);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($client, $guzzleClient);

        $this->expectException(FirecrawlApiException::class);

        $client->get('/test-endpoint');
    }
}
