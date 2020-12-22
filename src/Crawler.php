<?php

namespace SiteOrigin\KernelCrawler;

use Closure;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\LazyCollection;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Crawler extends LazyCollection
{

    public Queue $urlQueue;

    /**
     * @var \Illuminate\Support\HigherOrderCollectionProxy|mixed
     */
    private $kernel;

    public function __construct(...$startingUrls)
    {
        if(empty($startingUrls)) $startingUrls = ['/'];
        $this->urlQueue = new Queue(...$startingUrls);

        parent::__construct(Closure::fromCallable([$this, 'crawl']));
    }

    private function crawl()
    {
        if(empty($this->kernel)) $this->kernel = app()->make(HttpKernel::class);

        while (!$this->urlQueue->isEmpty()) {
            $item = $this->urlQueue->shift();

            // Get the response
            $symfonyRequest = SymfonyRequest::create($item->url);
            $request = Request::createFromBase($symfonyRequest);

            $response = $this->kernel->handle($request);
            $this->addUrlsFromResponse($response);
            yield new Exchange($request, $response);
        }
    }

    public function setKernel(HttpKernel $kernel = null)
    {
        $this->kernel = $kernel;
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
            if($link->attr('href')) $newUrls[] = $link->attr('href');
        });
        $newUrls = array_unique($newUrls);
        $this->urlQueue->push(...$newUrls);
    }
}