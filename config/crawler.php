<?php

return [
    'observers' => [
        'info' => \SiteOrigin\KernelCrawler\Crawler\Observer\CommandLineObserver::class,
        'sitemap' => \SiteOrigin\KernelCrawler\Crawler\Observer\SiteMapObserver::class,
    ],

    // Sitemap specific configurations
    'sitemap' => [
        'path' => public_path(),
        'filename' => 'sitemap.xml',
        'index' => 'sitemap-index.xml'
    ]
];