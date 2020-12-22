<?php

namespace SiteOrigin\KernelCrawler\Commands;

use Icamys\SitemapGenerator\SitemapGenerator;
use Illuminate\Console\Command;
use SiteOrigin\KernelCrawler\Crawler;
use SiteOrigin\KernelCrawler\Exchange;

class GenerateSitemap extends Command
{
    protected $signature = 'crawler:sitemap';
    protected $description = "Generate a sitemap from the main homepage.";

    public function handle()
    {
        // Setup the sitemaps
        $sitemap = new SitemapGenerator(config('app.url'), config('crawler.sitemap.path'));
        $sitemap->setSitemapFileName(config('crawler.sitemap.filename'));
        $sitemap->setSitemapIndexFilename(config('crawler.sitemap.index'));

        // Create a new crawler with the home URL as the starting point
        $crawler = new Crawler();
        $crawler
            ->takeUntilTimeout(now()->addMinutes(5))
            ->each(function(Exchange $exchange) use ($sitemap){
                if($exchange->response->status() == 200) {
                    $sitemap->addURL(
                        $exchange->request->url(),
                        new DateTime( $exchange->response->headers->get('last-modified') ?? 'now' ),
                        'daily',
                        0.5
                    );
                }
            });

        // Write the sitemaps
        $sitemap->createSitemap();
        $sitemap->writeSitemap();
    }
}