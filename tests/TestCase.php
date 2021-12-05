<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->clearCache();
       // these are to refresh configs and environment variables, since $app has loaded cache before it was cleaned
        $app->make(LoadEnvironmentVariables::class)->bootstrap($app);
        $app->make(LoadConfiguration::class)->bootstrap($app);

        return $app;
    }

    /**
     *
     */
    protected function clearCache ()
    {
        $commands = ['clear-compiled','cache:clear','view:clear','config:clear','route:clear'];

        foreach ($commands as $command) {
            Artisan::call($command);
        }
    }
}
