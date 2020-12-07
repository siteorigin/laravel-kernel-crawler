<?php

namespace SiteOrigin\KernelCrawler\Crawler;

use ArrayAccess;

class CrawlUrl implements ArrayAccess
{
    protected string $url;
    protected bool $processed = false;

    public function __construct(string $url, bool $processed = false)
    {
        $this->url = $url;
        $this->processed = $processed;
    }

    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset($offset)
    {
        $this->{$offset} = null;
    }

    public function setProcessed($status = true)
    {
        $this->processed = $status;
    }

    public function __toString(): string
    {
        return $this->url;
    }
}