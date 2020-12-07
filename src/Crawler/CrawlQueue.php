<?php

namespace SiteOrigin\KernelCrawler\Crawler;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CrawlQueue extends Collection
{
    protected array $knownUrls = [];

    /**
     * Converts a URL to a path, if it's relative to the
     *
     * @param $url
     * @return string|bool
     */
    public static function convertToPath($url)
    {
        $appUrl = config('app.url');

        // Remove external URLs
        if (! Str::startsWith($url, [$appUrl, '/'])) {
            return false;
        }

        if (Str::startsWith($url, $appUrl)) {
            $url = Str::replaceFirst($appUrl, '', $url);
        }

        if(empty($url)) return '/';
        else return $url;
    }

    /**
     * Add new URLs directly to this collection.
     *
     * @param array $urls A list of URLs
     * @return \SiteOrigin\KernelCrawler\Crawler\CrawlQueue
     */
    public function addUrlsArray(array $urls): CrawlQueue
    {
        $new = collect($urls)
            // Convert to paths, and remove any URLs that aren't from this site.
            ->map(fn($url) => static::convertToPath($url))
            ->filter()
            // Reject any that already exist
            ->reject(fn($url) => isset($this->knownUrls[$url]))
            // Push all the new URLs to the end of this collection
            ->each(function($url){
                $this->knownUrls[$url] = true;
                $this->push(new CrawlUrl($url));
            });

        return $this;
    }

    /**
     * @return \SiteOrigin\KernelCrawler\Crawler\CrawlUrl
     */
    public function getUnprocessedUrl(): ?CrawlUrl
    {
        return $this->where('processed', false)->first();
    }
}