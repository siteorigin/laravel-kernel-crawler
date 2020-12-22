<?php

namespace SiteOrigin\KernelCrawler\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use SiteOrigin\KernelCrawler\CrawlerServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
        $this->withoutExceptionHandling();
    }

    protected function getPackageProviders($app)
    {
        return [
            CrawlerServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $config = $app['config'];
        $config->set('filesystems.disks.page-cache', [
            'driver' => 'local',
            'root' => __DIR__ . '/storage/app/public/page-cache',
        ]);
        $config->set('view.paths', [
            __DIR__ . '/views'
        ]);
        $config->set('crawler', include(__DIR__.'/../config/crawler.php'));

        include __DIR__ . '/routes/web.php';

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'SiteOrigin\KernelCrawler\Tests\Database\Factories\\'.class_basename($modelName).'Factory';
        });
    }
}