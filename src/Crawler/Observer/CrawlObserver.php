<?php

namespace SiteOrigin\KernelCrawler\Crawler\Observer;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use SiteOrigin\KernelCrawler\Crawler\Crawler;
use SiteOrigin\KernelCrawler\Crawler\CrawlUrl;

abstract class CrawlObserver
{
    protected Crawler $crawler;

    public function setCrawler(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Called before a URL is crawled
     *
     * @param \SiteOrigin\KernelCrawler\Crawler\CrawlUrl $url
     * @param \Illuminate\Http\Request $request
     */
    public function beforeRequest(CrawlUrl $url, Request $request)
    {
        // Perform an action before the request is sent
    }

    /**
     * Called after a URL is called
     *
     * @param \SiteOrigin\KernelCrawler\Crawler\CrawlUrl $url
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     */
    public function afterRequest(CrawlUrl $url, Request $request, Response $response)
    {
        // Perform an action after the request is made and we have a response
    }

    /**
     * Handle an Exception
     *
     * @param \SiteOrigin\KernelCrawler\Crawler\CrawlUrl $url
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     */
    public function exception(CrawlUrl $url, Request $request, Exception $exception)
    {
        // Handle the error
    }

    public function crawlCompleted(Crawler $crawler)
    {
        // Perform an action when the crawl is completed
    }
}