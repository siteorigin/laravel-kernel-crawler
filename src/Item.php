<?php

namespace SiteOrigin\KernelCrawler;

use Illuminate\Support\Str;

class Item
{
    public string $url;

    public function __construct(string $url)
    {
        $this->url = self::urlToPath($url);
    }

    /**
     * Converts a URL to a path, if it's relative to the
     *
     * @param $url
     * @return string|bool
     */
    public static function urlToPath($url)
    {
        $url = url($url);
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
}