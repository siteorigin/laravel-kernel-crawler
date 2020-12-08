<?php

namespace SiteOrigin\KernelCrawler;

use Illuminate\Support\ServiceProvider;
use SiteOrigin\KernelCrawler\Commands\StartCrawler;
use SiteOrigin\KernelCrawler\Crawler\Crawler;

class KernelCrawlerServiceProvider extends ServiceProvider
{

    protected array $observers = [
        'info' => \SiteOrigin\KernelCrawler\Crawler\Observer\CommandLineObserver::class,
        'sitemap' => \SiteOrigin\KernelCrawler\Crawler\Observer\SiteMapObserver::class,
    ];

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                StartCrawler::class,
            ]);
        }

        $this->app->singleton(Crawler::class, function(){
            $crawler = new Crawler();

            $observers = array_merge($this->observers, (array) config('crawler.observers'));
            foreach($observers as $alias => $class) {
                $crawler->aliasObserver($alias, $class);
            }

            return $crawler;
        });
    }

    public function boot()
    {

    }
}