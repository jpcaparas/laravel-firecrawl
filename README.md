# Laravel Firecrawl API Client

[![run-tests](https://github.com/jpcaparas/laravel-firecrawl/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/jpcaparas/laravel-firecrawl/actions/workflows/run-tests.yml)

[![PHPStan](https://github.com/jpcaparas/laravel-firecrawl/actions/workflows/phpstan.yml/badge.svg)](https://github.com/jpcaparas/laravel-firecrawl/actions/workflows/phpstan.yml)

A Laravel package for seamlessly integrating with the [Firecrawl API](https://firecrawl.dev), allowing you to turn entire websites into LLM-ready markdown and extract structured data using AI.

## Features

- **Extract** - Get structured data from web pages with AI
- **Crawl** - Scrape all URLs from a website and get their content in LLM-ready format
- **Map** - Input a website and get all its URLs extremely fast
- **Scrape** - Get content from a single URL in various formats
- **Search** - Perform web searches with the Firecrawl API

## Installation

You can install the package via composer:

```bash
composer require jpcaparas/laravel-firecrawl
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-firecrawl-config"
```

## Configuration

Add your Firecrawl API key to your `.env` file:

```
FIRECRAWL_API_KEY=your_api_key_here
```

You can obtain an API key by signing up at [Firecrawl](https://firecrawl.dev).

## Usage

### Using the Facade

```php
use JPCaparas\LaravelFirecrawl\Facades\LaravelFirecrawl;

// Extract structured data from URLs
$result = LaravelFirecrawl::extract()->extract(
    ['https://example.com'],
    'Extract company information',
    [
        'type' => 'object',
        'properties' => [
            'company_name' => ['type' => 'string'],
            'description' => ['type' => 'string']
        ]
    ]
);

// Get extraction results using the job ID
$extractionResults = LaravelFirecrawl::extract()->getExtractionStatus($result['id']);
```

### Using Dependency Injection

```php
use JPCaparas\LaravelFirecrawl\LaravelFirecrawl;

public function __construct(protected LaravelFirecrawl $firecrawl)
{
    // Constructor injection
}

public function extractData()
{
    $result = $this->firecrawl->extract()->extract(
        ['https://example.com'],
        'Extract company information',
        [
            'type' => 'object',
            'properties' => [
                'company_name' => ['type' => 'string'],
                'description' => ['type' => 'string']
            ]
        ]
    );
    
    return $result;
}
```

### Available Services

#### Extract Service

```php
// Extract data from URLs using AI
$result = LaravelFirecrawl::extract()->extract(
    ['https://example.com'],
    'Extract company information',
    [
        'type' => 'object',
        'properties' => [
            'company_name' => ['type' => 'string'],
            'description' => ['type' => 'string']
        ]
    ],
    true // Enable web search (optional)
);

// Get extraction status and results
$status = LaravelFirecrawl::extract()->getExtractionStatus($result['id']);
```

#### Crawl Service

```php
// Crawl a website
$result = LaravelFirecrawl::crawl()->crawl('https://example.com', [
    'maxDepth' => 3,
    'allowExternalLinks' => false
]);

// Get crawl status and results
$status = LaravelFirecrawl::crawl()->getCrawlStatus($result['id']);
```

#### Map Service

```php
// Map a website to get all URLs
$result = LaravelFirecrawl::map()->map('https://example.com', [
    'maxDepth' => 2
]);
```

#### Scrape Service

```php
// Scrape a single URL
$result = LaravelFirecrawl::scrape()->scrape('https://example.com', [
    'outputFormat' => 'markdown',
    'includeScreenshot' => true
]);
```

#### Search Service

```php
// Search the web
$result = LaravelFirecrawl::search()->search('search query', [
    'limit' => 10
]);
```

### Command Line

You can view information about the Firecrawl API client using the included command:

```bash
php artisan firecrawl:info
```

This command displays:
- API key status
- Available services
- Usage examples

#### Extract Commands

Extract structured data from web pages with AI:

```bash
php artisan firecrawl:extract --urls=https://en.wikipedia.org/wiki/WD-40 --prompt="Extract product information" --schema='{"type":"object","properties":{"name":{"type":"string"},"description":{"type":"string"}}}'
```

Options:
- `--urls` - Array of URLs to extract data from (can be specified multiple times)
- `--urls-from-file` - Path to a file containing URLs, one per line
- `--prompt` - The prompt to guide the extraction
- `--prompt-from-file` - Path to a file containing the prompt text
- `--schema` - JSON schema for structuring the extracted data (inline JSON)
- `--schema-from-file` - Path to a JSON file containing the schema
- `--config-file` - Path to a JSON configuration file containing all extract parameters
- `--web-search` - Enable web search capability

- `--output` - Save results to specified file path

Check the status of an extraction job:

```bash
php artisan firecrawl:extract-status {id} --wait
```

Options:
- `--wait` - Wait for the extraction to complete
- `--poll-interval` - Seconds to wait between status checks when using --wait
- `--output` - Save results to specified file path

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
