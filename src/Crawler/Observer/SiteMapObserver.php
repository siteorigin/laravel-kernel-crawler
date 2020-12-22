<?php

namespace SiteOrigin\KernelCrawler\Crawler\Observer;

use DateTime;
use Icamys\SitemapGenerator\SitemapGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SiteOrigin\KernelCrawler\Crawler\Crawler;
use SiteOrigin\KernelCrawler\Crawler\CrawlUrl;

class SiteMapObserver extends CrawlObserver
{
    public function __construct()
    {
        $this->sitemap = new SitemapGenerator(config('app.url'), config('crawler.sitemap.path'));
        $this->sitemap->setSitemapFileName(config('crawler.sitemap.filename'));
        $this->sitemap->setSitemapIndexFilename(config('crawler.sitemap.index'));
    }

    public function afterRequest(CrawlUrl $url, Request $request, Response $response)
    {
        if($response->status() == 200) {
            $this->sitemap->addURL(
                $url['url'],
                new DateTime( $response->headers->get('last-modified') ?? 'now' ),
                'daily',
                0.5
            );
        }
    }

    public function crawlCompleted(Crawler $crawler)
    {
        $this->command->info('Writing sitemap.');

        $this->sitemap->createSitemap();
        $this->sitemap->writeSitemap();
    }
}