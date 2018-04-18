<?php

namespace Onemena\RdsListen;

use Illuminate\Support\ServiceProvider;
use Onemena\RdsListen\Commands\FrequentlyAliRDSQuery;
use Onemena\RdsListen\Commands\SlowAliRDS;

class RdsListenServiceProvider extends ServiceProvider
{
    /**
     * 服务提供者加是否延迟加载.
     *
     * @var bool
     */
    protected $defer = true; // 延迟加载服务
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', resource_path('views/vendor/rds-listen')); // 视图目录指定
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/rds-listen'),  // 发布视图目录到resources 下
            __DIR__.'/config/rds-listen.php' => config_path('rds-listen.php'), // 发布配置文件到 laravel 的config 下
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                FrequentlyAliRDSQuery::class,
                SlowAliRDS::class
            ]);
        }

    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__.'/config/rds-listen.php', 'rds-listen'
        );

        $this->app->singleton('rdslisten',function(){

            return $this->app->make(RdsListen::class);
        });
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        // 因为延迟加载 所以要定义 provides 函数 具体参考laravel 文档
        return [RdsListen::class];
    }
}
