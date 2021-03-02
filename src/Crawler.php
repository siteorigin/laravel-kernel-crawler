<?php

namespace SiteOrigin\KernelCrawler;

use Closure;
use Generator;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Crawler extends LazyCollection
{

    public PageQueue $urlQueue;

    /**
     * @var \Illuminate\Support\HigherOrderCollectionProxy|mixed
     */
    private $kernel;

    public function __construct(array $startingUrls = ['/'])
    {
        $this->urlQueue = new PageQueue($startingUrls);
        parent::__construct(Closure::fromCallable([$this, 'crawl']));
    }

    /**
     * Generator function for
     *
     * @return \Generator
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function crawl(): Generator
    {
        if(empty($this->kernel)) $this->kernel = app()->make(HttpKernel::class);

        while (!$this->urlQueue->isEmpty()) {
            $item = $this->urlQueue->shift();

            // Get the response
            $symfonyRequest = SymfonyRequest::create(config('app.url') . $item->url);
            $request = Request::createFromBase($symfonyRequest);

            $response = $this->kernel->handle($request);
            if($response instanceof Response) {
                $this->addUrlsFromResponse($response);
                yield new Exchange($request, $response);
            }
        }
    }

    /**
     * Set the HttpKernel to use for requests.
     *
     * @param \Illuminate\Contracts\Http\Kernel|null $kernel
     */
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
            $url = Page::urlToPath($link->attr('href'));
            if($url !== false) $newUrls[] = $url;
        });
        $newUrls = array_unique($newUrls);
        $this->urlQueue->push($newUrls);
    }
}