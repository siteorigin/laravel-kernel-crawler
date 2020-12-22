<?php

namespace SiteOrigin\KernelCrawler;

class PageQueue
{
    private array $items;
    private array $urls;

    public function __construct(array $urls)
    {
        $this->items = [];
        $this->urls = [];

        if(!is_array($urls)) $urls = [$urls];

        $this->push($urls);
    }

    /**
     * @param array|mixed $urls
     * @return \SiteOrigin\KernelCrawler\PageQueue
     */
    public function push(array $urls): PageQueue
    {
        $items = array_map(fn($url) => new Page($url), $urls);

        foreach ($items as $item) {
            // Skip URLs that are already in the queue
            if(isset($this->urls[$item->url])) continue;

            $this->items[] = $item;
            $this->urls[$item->url] = $item;
        }

        return $this;
    }

    public function shift(): Page
    {
        return array_shift($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

}