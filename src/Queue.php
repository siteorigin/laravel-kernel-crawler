<?php

namespace SiteOrigin\KernelCrawler;

class Queue
{
    private array $items;
    private array $urls;

    public function __construct(...$urls)
    {
        $this->items = [];
        $this->urls = [];

        if(!is_array($urls)) $urls = [$urls];

        $this->push(...$urls);
    }

    /**
     * @param array|mixed $urls
     * @return \SiteOrigin\KernelCrawler\Queue
     */
    public function push(...$urls): Queue
    {
        if(!is_array($urls)) $urls = [$urls];
        $items = array_map(fn($url) => new Item($url), $urls);

        foreach ($items as $item) {
            // Skip URLs that are already in the queue
            if(isset($this->urls[$item->url])) continue;

            $this->items[] = $item;
            $this->urls[$item->url] = $item;
        }

        return $this;
    }

    public function shift(): Item
    {
        return array_shift($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

}