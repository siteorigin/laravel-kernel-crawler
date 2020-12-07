<?php

namespace SiteOrigin\KernelCrawler\Crawler;

use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SiteOrigin\KernelCrawler\Crawler\Observer\CrawlObserver;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Crawler
{
    protected CrawlQueue $queue;
    protected Collection $observers;

    public function __construct(array $startUrls)
    {
        $this->queue = (new CrawlQueue())->addUrlsArray($startUrls);
        $this->observers = new Collection();
    }

    /**
     * Add new URLs to the crawler queue.
     *
     * @param array $urls
     * @return $this
     */
    public function addUrlsToQueue(array $urls): Crawler
    {
        $this->queue->addUrlsArray($urls);
        return $this;
    }

    /**
     * Add an observer/
     *
     * @param \SiteOrigin\KernelCrawler\Crawler\Observer\CrawlObserver $observer
     * @return $this
     */
    public function addObserver(CrawlObserver $observer): Crawler
    {
        $observer->setCrawler($this);
        $this->observers->push($observer);
        return $this;
    }

    protected function prepareUrlForRequest(string $uri): string
    {
        if (Str::startsWith($uri, '/')) {
            $uri = substr($uri, 1);
        }

        return trim(url($uri), '/');
    }

    /**
     * Start running the crawler
     */
    public function start()
    {
        $kernel = app()->make(HttpKernel::class);

        while(true) {
            $nextUrl = $this->queue->getUnprocessedUrl();
            if (empty($nextUrl)) break;

            // Get the response
            $symfonyRequest = SymfonyRequest::create($this->prepareUrlForRequest($nextUrl));
            $request = Request::createFromBase($symfonyRequest);

            // Call all the crawlers before the request is made
            $this->observers->each(fn($observer) => $observer->beforeRequest($nextUrl, $request));

            try {
                $response = $kernel->handle($request);
                $this->observers->each(fn($observer) => $observer->afterRequest($nextUrl, $request, $response));
            }
            catch(Exception $e) {
                $this->observers->each(fn($observer) => $observer->exception($nextUrl, $request, $e));
            }

            $this->addUrlsFromResponse($response);
            $nextUrl['processed'] = true;
        }

        $this->observers->each(fn($observer) => $observer->crawlCompleted($this));
    }

    /**
     * Add any new URLs discovered in this response.
     *
     * @param Response $response
     */
    protected function addUrlsFromResponse(Response $response)
    {
        $newUrls = [];

        $crawler = new DomCrawler($response->getContent());
        $crawler->filterXPath('//a')->each(function($link) use (& $newUrls){
            $newUrls[] = $link->attr('href');
        });

        $newUrls = array_unique($newUrls);

        $this->queue->addUrlsArray($newUrls);
    }

    /**
     * @return \SiteOrigin\KernelCrawler\Crawler\CrawlQueue
     */
    public function getQueue()
    {
        return $this->queue;
    }
}