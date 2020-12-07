<?php

namespace SiteOrigin\KernelCrawler;

use Illuminate\Support\ServiceProvider;
use SiteOrigin\KernelCrawler\Commands\RunCrawler;

class KernelCrawlerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            RunCrawler::class,
        ]);
    }

    public function boot()
    {

    }
}