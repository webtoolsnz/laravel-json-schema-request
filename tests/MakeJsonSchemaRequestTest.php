<?php

namespace Webtools\JsonSchemaRequest\Tests;

use Orchestra\Testbench\TestCase;
use Webtools\JsonSchemaRequest\JsonSchemaRequestServiceProvider;

class MakeJsonSchemaRequestTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [JsonSchemaRequestServiceProvider::class];
    }

    /**
     * @test
     */
    public function it_should_create_a_request_class()
    {
        $this->artisan('make:json-request', ['name' => 'MySchemaRequest'])->assertExitCode(0);
    }
}