<?php

namespace SiteOrigin\KernelCrawler\Commands;

use Illuminate\Console\Command;
use SiteOrigin\KernelCrawler\Facades\Crawler;

class StartCrawler extends Command
{
    protected $signature = 'crawler:start {--observer=* : Observer name or alias} {--silent}';
    protected $description = "Crawl the site, starting with the given URL.";

    public function handle()
    {
        // Create a new crawler with the home URL as the starting point
        Crawler::reset(['/']);

        if (!$this->option('silent')) {
            Crawler::addObserver('info', $this);
        }

        foreach($this->option('observer') as $observer) {
            // Add the observer using the service container.
            Crawler::addObserver($observer, $this);
        }

        Crawler::start();
    }
}