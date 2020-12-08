<?php

namespace SiteOrigin\KernelCrawler\Facades;

use Illuminate\Support\Facades\Facade;

class Crawler extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \SiteOrigin\KernelCrawler\Crawler\Crawler::class;
    }
}