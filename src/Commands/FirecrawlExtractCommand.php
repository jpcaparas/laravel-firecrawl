<?php

namespace JPCaparas\LaravelFirecrawl\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use JPCaparas\LaravelFirecrawl\Exceptions\FirecrawlApiException;
use JPCaparas\LaravelFirecrawl\Exceptions\InvalidConfigurationException;
use JPCaparas\LaravelFirecrawl\LaravelFirecrawl;

class FirecrawlExtractCommand extends Command
{
    public $signature = 'firecrawl:extract
                          {--urls=* : Array of URLs to extract data from (can be specified multiple times)}
                          {--urls-from-file= : Path to a file containing URLs, one per line}
                          {--prompt= : The prompt to guide the extraction}
                          {--prompt-from-file= : Path to a file containing the prompt text}
                          {--schema= : JSON schema for structuring the extracted data (inline JSON)}
                          {--schema-from-file= : Path to a JSON file containing the schema}
                          {--config-file= : Path to a JSON configuration file containing all extract parameters}
                          {--web-search : Enable web search capability}
                          {--api-key= : Override the API key from config}
                          {--output= : Save results to specified file path}';

    public $description = 'Extract structured data from web pages with AI using Firecrawl';

    protected LaravelFirecrawl $firecrawl;

    public function __construct(LaravelFirecrawl $firecrawl)
    {
        parent::__construct();
        $this->firecrawl = $firecrawl;
    }

    public function handle(): int
    {
        $this->info('🔍 Firecrawl Extract - Structured Data Extraction');
        $this->newLine();

        // Initialize API with custom key if provided
        $apiKey = $this->option('api-key') ?: config('firecrawl.api_key');
        if (! empty($this->option('api-key'))) {
            $this->firecrawl = new LaravelFirecrawl($this->option('api-key'));
        }

        // Check if config file is provided and load it
        if ($configFile = $this->option('config-file')) {
            return $this->handleConfigFile($configFile);
        }

        // Gather URLs
        $urls = $this->getUrls();
        if (empty($urls)) {
            $this->error('No URLs provided. Use --urls or --urls-from-file options');

            return self::FAILURE;
        }

        // Get prompt
        $prompt = $this->getPrompt();
        if (empty($prompt)) {
            $this->error('No prompt provided. Use --prompt or --prompt-from-file options');

            return self::FAILURE;
        }

        // Get schema
        $schema = $this->getSchema();
        if (empty($schema)) {
            $this->error('No schema provided. Use --schema or --schema-from-file options');

            return self::FAILURE;
        }

        // Check for web search flag
        $enableWebSearch = $this->option('web-search');

        // Execute the extraction
        try {
            $this->info('Starting extraction with the following parameters:');
            $this->line('URLs: '.implode(', ', $urls));
            $this->line('Prompt: '.$prompt);
            $this->line('Web Search: '.($enableWebSearch ? 'Enabled' : 'Disabled'));
            $this->newLine();

            $this->line('Sending request to Firecrawl API...');
            $response = $this->firecrawl->extract()->extract(
                $urls,
                $prompt,
                $schema,
                $enableWebSearch
            );

            $extractId = $response['id'] ?? null;

            if (! $extractId) {
                $this->error('Failed to get extraction ID from response');

                return self::FAILURE;
            }

            $this->info("Extraction job started with ID: {$extractId}");

            // If the status key is missing, output the full response
            if (! isset($response['status'])) {
                $this->line('Full response:');
                $this->line(json_encode($response, JSON_PRETTY_PRINT));

                return self::FAILURE;
            }

            $this->line("Status: {$response['status']}");

            $this->info('Use the following command to check status:');
            $this->line("php artisan firecrawl:extract-status {$extractId}");

            return self::SUCCESS;
        } catch (FirecrawlApiException|InvalidConfigurationException $e) {
            $this->error("API Error: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    /**
     * Handle extraction using a config file
     */
    protected function handleConfigFile(string $configFile): int
    {
        if (! File::exists($configFile)) {
            $this->error("Config file not found: {$configFile}");

            return self::FAILURE;
        }

        try {
            $config = json_decode(File::get($configFile), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON in config file: '.json_last_error_msg());

                return self::FAILURE;
            }

            $urls = $config['urls'] ?? [];
            $prompt = $config['prompt'] ?? '';
            $schema = $config['schema'] ?? [];
            $enableWebSearch = $config['enableWebSearch'] ?? false;

            if (empty($urls) || empty($prompt) || empty($schema)) {
                $this->error("Config file must contain 'urls', 'prompt', and 'schema' fields");

                return self::FAILURE;
            }

            $this->info('Starting extraction with config file parameters');
            $this->line('URLs: '.implode(', ', $urls));
            $this->line('Prompt: '.$prompt);
            $this->line('Web Search: '.($enableWebSearch ? 'Enabled' : 'Disabled'));
            $this->newLine();

            $response = $this->firecrawl->extract()->extract(
                $urls,
                $prompt,
                $schema,
                $enableWebSearch
            );

            $extractId = $response['id'] ?? null;

            if (! $extractId) {
                $this->error('Failed to get extraction ID from response');

                return self::FAILURE;
            }

            $this->info("Extraction job started with ID: {$extractId}");

            $this->info('Use the following command to check status:');
            $this->line("php artisan firecrawl:extract-status {$extractId}");

            return self::SUCCESS;
        } catch (FirecrawlApiException|InvalidConfigurationException $e) {
            $this->error("API Error: {$e->getMessage()}");

            return self::FAILURE;
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    /**
     * Get URLs from options
     */
    protected function getUrls(): array
    {
        $urls = $this->option('urls');

        if ($urlsFile = $this->option('urls-from-file')) {
            if (! File::exists($urlsFile)) {
                $this->warn("URLs file not found: {$urlsFile}");
            } else {
                $fileUrls = collect(explode("\n", File::get($urlsFile)))
                    ->map(fn ($line) => trim($line))
                    ->filter()
                    ->toArray();

                $urls = array_merge($urls, $fileUrls);
            }
        }

        return $urls;
    }

    /**
     * Get prompt from options
     */
    protected function getPrompt(): string
    {
        $prompt = $this->option('prompt') ?? '';

        if ($promptFile = $this->option('prompt-from-file')) {
            if (! File::exists($promptFile)) {
                $this->warn("Prompt file not found: {$promptFile}");
            } else {
                $prompt = trim(File::get($promptFile));
            }
        }

        return $prompt;
    }

    /**
     * Get schema from options
     */
    protected function getSchema(): array
    {
        $schema = [];
        $schemaOption = $this->option('schema');

        if (! empty($schemaOption)) {
            try {
                $schema = json_decode($schemaOption, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->warn('Invalid schema JSON: '.json_last_error_msg());
                    $schema = [];
                }
            } catch (\Exception $e) {
                $this->warn('Failed to parse schema JSON: '.$e->getMessage());
            }
        }

        if ($schemaFile = $this->option('schema-from-file')) {
            if (! File::exists($schemaFile)) {
                $this->warn("Schema file not found: {$schemaFile}");
            } else {
                try {
                    $schema = json_decode(File::get($schemaFile), true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $this->warn('Invalid schema in file: '.json_last_error_msg());
                        $schema = [];
                    }
                } catch (\Exception $e) {
                    $this->warn('Failed to parse schema file: '.$e->getMessage());
                }
            }
        }

        return $schema;
    }
}
