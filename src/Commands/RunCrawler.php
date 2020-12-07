<?php

namespace SiteOrigin\KernelCrawler\Commands;

use Exception;
use Illuminate\Console\Command;
use SiteOrigin\KernelCrawler\Crawler\Crawler;
use SiteOrigin\KernelCrawler\Crawler\Observer\CommandLineObserver;

class RunCrawler extends Command
{

    protected $signature = 'crawler:run {--observer=* : Observer name or alias} {--entry="/"} {--silent}';
    protected $description = "Crawl the site, starting with the given URL.";

    public function handle()
    {
        // Create a new crawler with the home URL as the starting point
        $crawler = new Crawler(['/']);

        if (!$this->option('silent')) {
            $crawler->addObserver(new CommandLineObserver($this));
        }

        foreach($this->option('observer') as $observer) {
            $class = config('crawler.observers')[$observer] ?? $observer;
            if (!class_exists($class)) {
                $this->error('Observer class [' . $observer . '] not found - quitting.');
                return;
            }

            // Add the observer using the service container.
            $observer = resolve($class);
            $observer->setCommand($this);
            $crawler->addObserver($observer);
        }

        $crawler->start();
    }
}