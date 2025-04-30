<?php

namespace JPCaparas\LaravelFirecrawl\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use JPCaparas\LaravelFirecrawl\Exceptions\FirecrawlApiException;
use JPCaparas\LaravelFirecrawl\Exceptions\InvalidConfigurationException;
use JPCaparas\LaravelFirecrawl\LaravelFirecrawl;

class FirecrawlExtractStatusCommand extends Command
{
    public $signature = 'firecrawl:extract-status
                          {id : The extraction job ID}
                          {--api-key= : Override the API key from config}
                          {--wait : Wait for the extraction to complete}
                          {--poll-interval=5 : Seconds to wait between status checks when using --wait}
                          {--output= : Save results to specified file path}';

    public $description = 'Check the status of a Firecrawl extract job';

    protected LaravelFirecrawl $firecrawl;

    public function __construct(LaravelFirecrawl $firecrawl)
    {
        parent::__construct();
        $this->firecrawl = $firecrawl;
    }

    public function handle(): int
    {
        $extractId = $this->argument('id');

        // Initialize API with custom key if provided
        if (! empty($this->option('api-key'))) {
            $this->firecrawl = new LaravelFirecrawl($this->option('api-key'));
        }

        try {
            if ($this->option('wait')) {
                return $this->waitForCompletion($extractId);
            } else {
                return $this->checkStatus($extractId);
            }
        } catch (FirecrawlApiException|InvalidConfigurationException $e) {
            $this->error("API Error: {$e->getMessage()}");

            return self::FAILURE;
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    /**
     * Check the current status of an extraction job
     */
    protected function checkStatus(string $extractId): int
    {
        $this->info("Checking status for extraction job: {$extractId}");

        $response = $this->firecrawl->extract()->getExtractionStatus($extractId);
        $status = $response['status'] ?? 'unknown';

        $this->info("Status: {$status}");

        if ($status === 'completed') {
            $this->displayResults($response);

            // Save output if requested
            if ($outputFile = $this->option('output')) {
                File::put($outputFile, json_encode($response, JSON_PRETTY_PRINT));
                $this->info("Results saved to: {$outputFile}");
            }
        } elseif ($status === 'failed') {
            $this->error('Extraction failed');
            if (isset($response['error'])) {
                $this->line("Error: {$response['error']}");
            }
        } else {
            $this->line('The job is still processing. Use --wait option to poll until completion.');
        }

        return self::SUCCESS;
    }

    /**
     * Poll for extraction status until complete
     */
    protected function waitForCompletion(string $extractId): int
    {
        $this->info("Waiting for extraction job to complete: {$extractId}");
        $interval = (int) $this->option('poll-interval');
        $completed = false;
        $result = null;

        while (! $completed) {
            $response = $this->firecrawl->extract()->getExtractionStatus($extractId);
            $status = $response['status'] ?? 'unknown';

            $this->line("Current status: {$status}");

            if ($status === 'completed' || $status === 'failed') {
                $completed = true;
                $result = $response;
            } else {
                $this->line("Checking again in {$interval} seconds...");
                sleep($interval);
            }
        }

        if ($result['status'] === 'completed') {
            $this->info('Extraction completed successfully!');
            $this->displayResults($result);

            // Save output if requested
            if ($outputFile = $this->option('output')) {
                File::put($outputFile, json_encode($result, JSON_PRETTY_PRINT));
                $this->info("Results saved to: {$outputFile}");
            }

            return self::SUCCESS;
        } else {
            $this->error('Extraction failed');
            if (isset($result['error'])) {
                $this->line("Error: {$result['error']}");
            }

            return self::FAILURE;
        }
    }

    /**
     * Display the extraction results
     */
    protected function displayResults(array $response): void
    {
        $this->newLine();
        $this->info('Results:');

        if (isset($response['data'])) {
            $this->line(json_encode($response['data'], JSON_PRETTY_PRINT));
        } else {
            $this->warn('No result data available');
        }
    }
}
