<?php namespace Wongyip\Laravel\Renderable;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class RenderableServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        // $this->mergeConfigFrom(__DIR__ . '/../config/config_name.php', 'config_name');
    }
    
    /**
     * Register any other events for your application.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        // [Pinnd] DO NOT invoke DB in service provider's boot() method.
        
        // Config to constants
        if (!defined('LARAVEL_RENDERABLE_DATETIME_FORMAT')) {
            define('LARAVEL_RENDERABLE_DATETIME_FORMAT', config('renderable.datetime_format', 'Y-m-d H:i:s'));
        }
        if (!defined('LARAVEL_RENDERABLE_VIEW_NAMESPACE')) {
            define('LARAVEL_RENDERABLE_VIEW_NAMESPACE', config('renderable.view_namespace', 'renderable'));
        }
        
        /**
         * Package views, referenced using a double-colon package::view syntax.
         * @see https://laravel.com/docs/5.2/packages#views
         */
        $this->loadViewsFrom(__DIR__ . '/../views', LARAVEL_RENDERABLE_VIEW_NAMESPACE);
        
        /**
         * Publish package files to project.
         * Note that publishing run while executing artisan vendor:publish command.
         */
        
        // Views
        $this->publishes([
            __DIR__ . '/../views' => resource_path('views/vendor/renderable'),
        ], 'views');
        
        // Supporting JS & CSS
        $this->publishes([
            __DIR__ . '/../assets' => public_path('vendor/renderable'),
        ], 'public');
        
    }
}