<?php

namespace SiteOrigin\KernelCrawler\Crawler\Observer;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SiteOrigin\KernelCrawler\Crawler\Crawler;
use SiteOrigin\KernelCrawler\Crawler\CrawlUrl;

class CommandLineObserver extends CrawlObserver
{
    /**
     * @var \Illuminate\Console\Command
     */
    private Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function afterRequest(CrawlUrl $url, Request $request, Response $response)
    {
        $this->command->info('Processed: ' . $url);
    }

    public function crawlCompleted(Crawler $crawler)
    {

        $this->command->info('===============');
        $this->command->info('Crawl Completed');
        $this->command->info('Pages processed: ' . $crawler->getQueue()->count());
    }
}