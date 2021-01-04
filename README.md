# Laravel Kernel Crawler

This package gives you a local site crawler based on a Laravel LazyCollection. Each request goes directly through the local HTTPKernel, so it doesn't generate web server requests.

2 examples of you might need this is for warming your site's cache, and generating a sitemap. This package offers commands for both of these.

It's currently compatible with Laravel 8.0+

## Installation

*Installation instructions coming once this package is on Packagist.*

To publish the configuration files, use:

`php artisan vendor:publish --provider="SiteOrigin\KernelCrawler\CrawlerServiceProvider" --tag="config"`

## Usage

Here's a very basic use example:

```php
use SiteOrigin\KernelCrawler\Crawler;
use SiteOrigin\KernelCrawler\Exchange;


$crawler = new Crawler();
$crawler->each(function(Exchange $ex){
	// $ex->request is an Illuminate\Http\Request object
	$ex->request->url();
	// $ex->response is an Illuminate\Http\Response object
	$ex->response->getContent()
});
```

After creating a Crawler, you can use all of the Laravel [LazyCollection](https://laravel.com/docs/8.x/collections#lazy-collections) functions. This offers a collection of [`Exchange`](https://github.com/siteorigin/laravel-kernel-crawler/blob/develop/src/Exchange.php) objects.

## Warming Cache

If all you need to do is generate dummy requests to each public URL on your site, then you can use `php arisan crawler:start`. This is useful if you want to warm the cache for all public URLs on your site.

## Generating a Sitemap

This package offers the command `php artisan crawler:sitemap` to generate the sitemap. You can configure your sitemap using the [crawler.php](https://github.com/siteorigin/laravel-kernel-crawler/blob/develop/config/crawler.php) configuration file. See the Installation section for instructions on publishing this config file.