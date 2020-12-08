<?php

namespace SiteOrigin\KernelCrawler\Crawler;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use SiteOrigin\KernelCrawler\Crawler\Observer\CrawlObserver;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Crawler
{
    protected CrawlQueue $queue;
    protected Collection $observers;

    protected array $observerAliases = [];
    protected bool $isCrawling = false;

    public function __construct(array $startUrls = [])
    {
        $this->reset($startUrls);
    }

    /**
     * @param array $startUrls
     */
    public function reset(array $startUrls = [])
    {
        $this->queue = (new CrawlQueue())->addNewUrls($startUrls);
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
        $this->queue->addNewUrls($urls);
        return $this;
    }

    /**
     * Add an observer/
     *
     * @param \SiteOrigin\KernelCrawler\Crawler\Observer\CrawlObserver|string $observer
     * @param \Illuminate\Console\Command|null $command
     * @return $this
     */
    public function addObserver($observer, ?Command $command = null): Crawler
    {
        // See if we can resolve a class from a string input
        if (is_string($observer)) {
            if(isset($this->observerAliases[$observer]) && class_exists($this->observerAliases[$observer])) {
                $observer = new $this->observerAliases[$observer];
            }
            elseif (class_exists($observer)) {
                $observer = new $observer;
            }
        }

        // By this point, we should have a CrawlObserver object

        if (is_subclass_of($observer, CrawlObserver::class)){
            $observer->setCrawler($this);
            if (!is_null($command)) $observer->setCommand($command);
            $this->observers->push($observer);
        }
        else {
            throw new InvalidArgumentException(
                'Observer must be an alias, classname, or CrawlObserver object. ' .
                '"' . (is_string($observer) ? $observer : get_class($observer)) . '" given.'
            );
        }

        return $this;
    }

    /**
     * Alias an observer.
     *
     * @param $name
     * @param $classname
     */
    public function aliasObserver($name, $classname)
    {
        $this->observerAliases[$name] = $classname;
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
        $this->isCrawling = true;
        $kernel = app()->make(HttpKernel::class);
        $this->observers->each(fn($observer) => $observer->crawlStarting($this, $kernel));

        while(true) {
            $nextUrl = $this->queue->shiftUrl();
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
        $this->isCrawling = false;
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

        $this->queue->addNewUrls($newUrls);
    }

    /**
     * @return \SiteOrigin\KernelCrawler\Crawler\CrawlQueue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    public function isCrawling()
    {
        return $this->isCrawling;
    }
}