<?php

namespace Trikey\FileUploader;

use Illuminate\Support\ServiceProvider;

class FileUploaderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'file-uploader');
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }
}
