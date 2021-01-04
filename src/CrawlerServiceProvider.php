<?php

namespace SiteOrigin\KernelCrawler;

use Illuminate\Support\ServiceProvider;
use SiteOrigin\KernelCrawler\Commands\CrawlSite;
use SiteOrigin\KernelCrawler\Commands\GenerateSitemap;

class CrawlerServiceProvider extends ServiceProvider
{

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CrawlSite::class,
                GenerateSitemap::class
            ]);
        }
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/crawler.php' => config_path('crawler.php'),
        ], 'config');
    }
}