<?php

namespace SiteOrigin\KernelCrawler;

use Illuminate\Support\ServiceProvider;
use SiteOrigin\KernelCrawler\Commands\CrawlSite;
use SiteOrigin\KernelCrawler\Crawler\Crawler;

class KernelCrawlerServiceProvider extends ServiceProvider
{

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CrawlSite::class,
            ]);
        }
    }

    public function boot()
    {

    }
}