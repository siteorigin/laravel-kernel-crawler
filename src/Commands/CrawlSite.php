<?php

namespace SiteOrigin\KernelCrawler\Commands;

use Illuminate\Console\Command;
use SiteOrigin\KernelCrawler\Crawler;
use SiteOrigin\KernelCrawler\Exchange;

class CrawlSite extends Command
{
    protected $signature = 'crawler:start';
    protected $description = "Crawl the site, starting with the given URL.";

    public function handle()
    {
        // Create a new crawler with the home URL as the starting point
        $crawler = new Crawler();
        $crawler
            ->each(function(Exchange $exchange){
                $this->info('Crawled: ' . $exchange->request->url());
            });
    }
}