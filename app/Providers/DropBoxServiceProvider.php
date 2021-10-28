<?php

namespace App\Providers;

use Storage;
use Illuminate\Support\ServiceProvider;

use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropBoxServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('dropbox', function ($app, $config) {
            $client = new Client([$config['key'], $config['secret']]);
            return new Filesystem(new DropboxAdapter($client));
        });
    }
}
