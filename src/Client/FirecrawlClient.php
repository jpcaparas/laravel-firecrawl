<?php

namespace JPCaparas\LaravelFirecrawl\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;
use JPCaparas\LaravelFirecrawl\Exceptions\FirecrawlApiException;
use JPCaparas\LaravelFirecrawl\Exceptions\InvalidConfigurationException;

class FirecrawlClient
{
    protected GuzzleClient $client;

    protected ?string $apiKey;

    protected string $baseUrl = 'https://api.firecrawl.dev/v1/';

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? Config::get('firecrawl.api_key');

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        // Only add the Authorization header if we have an API key
        if (! empty($this->apiKey)) {
            $headers['Authorization'] = "Bearer {$this->apiKey}";
        }

        $this->client = new GuzzleClient([
            'base_uri' => $this->baseUrl,
            'headers' => $headers,
        ]);
    }

    /**
     * Make a GET request to the Firecrawl API
     *
     * @throws FirecrawlApiException
     * @throws InvalidConfigurationException
     */
    public function get(string $endpoint, array $queryParams = []): array
    {
        $this->validateApiKey();

        return $this->request('GET', $endpoint, ['query' => $queryParams]);
    }

    /**
     * Make a POST request to the Firecrawl API
     *
     * @throws FirecrawlApiException
     * @throws InvalidConfigurationException
     */
    public function post(string $endpoint, array $data = []): array
    {
        $this->validateApiKey();

        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    /**
     * Make a request to the Firecrawl API
     *
     * @throws FirecrawlApiException
     * @throws InvalidConfigurationException
     */
    protected function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new FirecrawlApiException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Validate that the API key is set
     *
     * @throws InvalidConfigurationException
     */
    protected function validateApiKey(): void
    {
        if (empty($this->apiKey)) {
            throw new InvalidConfigurationException('Firecrawl API key is not set');
        }

        // Check if we have a client that has a 'handler' in config, which means it's a mock for testing
        /** @phpstan-ignore-next-line */
        $clientConfig = $this->client->getConfig();
        $isMockClient = isset($clientConfig['handler']) && ! isset($clientConfig['base_uri']);

        // Only update the client if it's not a mock and doesn't have Authorization header
        if (! $isMockClient && ! isset($clientConfig['headers']['Authorization'])) {
            $this->client = new GuzzleClient([
                'base_uri' => $this->baseUrl,
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);
        }
    }
}
