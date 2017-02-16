<?php

namespace dees040\Laracrumbs;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class LaracrumbsServiceProvider extends ServiceProvider
{
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laracrumbs'];
    }

    /**
     * Boot service provider.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'laracrumbs');

//        $this->registerHelpers();
    }

    /**
     * Register service provider.
     */
    public function register()
    {
        $this->app->singleton('laracrumbs', function ($app) {
            return $this->app->make('dees040\Laracrumbs\Laracrumbs');
        });

        $this->registerBreadcrumbs();
    }

    /**
     * Register the breadcrumbs.
     */
    private function registerBreadcrumbs()
    {
        $this->registerFile($this->app['path'].'/Http/breadcrumbs.php', true);
    }

    /**
     * Register the helper file.
     */
    private function registerHelpers()
    {
        $this->registerFile(__DIR__ . '/helpers.php');
    }

    /**
     * Require the given file.
     *
     * @param  string  $file
     * @throws \dees040\Laracrumbs\Exceptions\FileNotFoundException
     */
    private function registerFile($file, $once = false)
    {
        if (! file_exists($file)) {
            throw new FileNotFoundException($file . " does not exists.");
        }

        if ($once) {
            require_once $file;
        } else {
            require $file;
        }
    }
}