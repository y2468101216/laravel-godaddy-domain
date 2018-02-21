<?php

namespace Y2468101216\Godaddy;

use Illuminate\Support\ServiceProvider;
use Y2468101216\Godaddy\Console\DomainCommand;


class DomainServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->pubishes([
            __DIR__.'/../config/godaddy.php' => config_path('godaddy.php'),
        ]);

        $this->commands([
            DomainCommand::class,
        ]);
    }
}