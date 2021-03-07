<?php

namespace YllwDigital\YllwAuth;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

use YllwDigital\YllwAuth\Console\InstallYllwAuth;

use YllwDigital\YllwAuth\app\Services\LoginService;
use YllwDigital\YllwAuth\app\Services\PasswordService;
use YllwDigital\YllwAuth\app\Services\RegisterService;

class YllwAuthServiceProvider extends ServiceProvider {
    protected $configRoute = __DIR__.'/config/config.php';
    // protected $apiRoute    = __DIR__.'/../routes/api.php';

    public function boot() {
        $this->registerRoutes();
        $this->publishConfig();

        if($this->app->runningInConsole()) {
            $this->commands([
                InstallYllwAuth::class
            ]);
        }
    }

    protected function registerRoutes() {
        // Route::group($this->routeConfiguration(), function() {
        //     $this->loadRoutesFrom($this->apiRoute);
        // });
    }

    protected function routeConfiguration() {
        return [
            'prefix'     => 'api',
            'middleware' => ['api']
        ];
    }

    protected function publishConfig() {
        if($this->app->runningInConsole()) {
            $this->publishes([
                $this->configRoute => config_path('yllwauth.php')
            ], 'config');
        }
    }

    public function register() {
        $this->registerConfig();
        $this->registerServiceProviders();
    }

    protected function registerConfig() {
        $this->mergeConfigFrom($this->configRoute, 'yllwauth');
    }

    protected function registerServiceProviders() {
        $this->app->bind('yllwLogin', function($app) {
            return new LoginService();
        });

        $this->app->bind('yllwRegister', function($app) {
            return new RegisterService();
        });

        $this->app->bind('yllwPassword', function($app) {
            return new PasswordService();
        });
    }
}