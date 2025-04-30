<?php

namespace JPCaparas\LaravelFirecrawl\Commands;

use Illuminate\Console\Command;
use JPCaparas\LaravelFirecrawl\LaravelFirecrawl;

class LaravelFirecrawlCommand extends Command
{
    public $signature = 'firecrawl:info';

    public $description = 'Display information about the Firecrawl API client';

    protected LaravelFirecrawl $firecrawl;

    public function __construct(LaravelFirecrawl $firecrawl)
    {
        parent::__construct();
        $this->firecrawl = $firecrawl;
    }

    public function handle(): int
    {
        $this->info('📦 Laravel Firecrawl Package');
        $this->newLine();

        // Check if API key is set
        $apiKeyStatus = config('firecrawl.api_key') ? '✅ Set' : '❌ Not set';

        $this->line("<comment>API Key status:</comment> {$apiKeyStatus}");

        if (! config('firecrawl.api_key')) {
            $this->warn('You need to set your Firecrawl API key in your .env file:');
            $this->line('FIRECRAWL_API_KEY=your_api_key_here');
        }

        $this->newLine();
        $this->info('Available Services:');
        $this->line(' - Crawl: Scrape all the URLs of a web page');
        $this->line(' - Extract: Get structured data from web pages with AI');
        $this->line(' - Map: Get all URLs from a website quickly');
        $this->line(' - Scrape: Get content from a single URL');
        $this->line(' - Search: Search the web with the Firecrawl API');

        $this->newLine();
        $this->info('Usage Examples:');
        $this->line('<comment>// Crawl a website</comment>');
        $this->line("\$result = app('laravel-firecrawl')->crawl()->crawl('https://example.com');");

        $this->newLine();
        $this->line('<comment>// Extract structured data</comment>');
        $this->line("\$result = app('laravel-firecrawl')->extract()->extract(['https://example.com'], 'Extract company info', ['type' => 'object']);");

        $this->newLine();
        $this->line('<comment>// Or use the facade</comment>');
        $this->line("\$result = \JPCaparas\LaravelFirecrawl\Facades\LaravelFirecrawl::extract()->extract(['https://example.com'], 'Extract company info', ['type' => 'object']);");

        return self::SUCCESS;
    }
}
