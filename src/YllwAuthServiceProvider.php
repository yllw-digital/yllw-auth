<?php

namespace YllwDigital\YllwAuth;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use YllwDigital\YllwAuth\Console\InstallYllwAuth;

class YllwAuthServiceProvider extends ServiceProvider {

    public function boot() {
        $this->registerRoutes();

        if($this->app->runningInConsole()) {
            $this->commands([
                InstallYllwAuth::class
            ]);
        }
    }

    protected function registerRoutes() {
        Route::group($this->routeConfiguration(), function() {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    protected function routeConfiguration() {
        return [
            'prefix'     => 'api',
            'middleware' => ['api']
        ];
    }

    public function register() {
    }
}