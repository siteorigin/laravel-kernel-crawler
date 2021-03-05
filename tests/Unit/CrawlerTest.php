<?php

namespace SiteOrigin\KernelCrawler\Tests\Unit;

use SiteOrigin\KernelCrawler\Crawler;
use SiteOrigin\KernelCrawler\Exchange;
use SiteOrigin\KernelCrawler\Tests\App\Article;
use SiteOrigin\KernelCrawler\Tests\TestCase;

class CrawlerTest extends TestCase
{
    public function test_crawling_site()
    {
        Article::factory()->count(10)->create();
        $crawler = new Crawler();
        $urls = [];
        $crawler->each(function(Exchange $r) use (& $urls){
            $urls[] = $r->request->url();
        });

        $this->assertCount(17, $urls);
    }

    public function test_crawl_command()
    {
        Article::factory()->count(10)->create();
        $c = $this->artisan('crawler:start', []);

        // Check at least 2 of the pages were crawled
        $c->expectsOutput('Crawled: ' . route('home'));
        $c->expectsOutput('Crawled: ' . route('articles.show', Article::all()->first()));
    }
}