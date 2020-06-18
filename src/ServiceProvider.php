<?php

namespace Webtools\JsonSchemaRequest;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->resolving(JsonSchemaRequest::class, function ($request, $app) {
            $request = JsonSchemaRequest::createFrom($app['request'], $request);
            $request->setContainer($app);
        });
    }
}
