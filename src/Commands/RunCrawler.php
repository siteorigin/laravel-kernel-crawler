<?php

namespace SiteOrigin\KernelCrawler\Commands;

use Illuminate\Console\Command;
use SiteOrigin\KernelCrawler\Crawler\Crawler;
use SiteOrigin\KernelCrawler\Crawler\Observer\CommandLineObserver;

class RunCrawler extends Command
{

    protected $signature = 'crawler:run {--start="/"}';
    protected $description = "Crawl the site, starting with the given URL.";

    public function handle()
    {
        // Create a new crawler with the home URL as the starting point
        $crawler = new Crawler(['/']);
        $crawler->addObserver(new CommandLineObserver($this));
        $crawler->start();
    }
}