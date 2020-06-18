<?php

namespace Webtools\JsonSchemaRequest\Tests;

use Orchestra\Testbench\TestCase;
use Webtools\JsonSchemaRequest\JsonSchemaRequestServiceProvider;
use Webtools\JsonSchemaRequest\Tests\Support\ApiRequest;
use Illuminate\Routing\Router;

class JsonSchemaRequestTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [JsonSchemaRequestServiceProvider::class];
    }

    /**
     * @test
     */
    public function controllers_should_reject_invalid_json_resolve()
    {
        $router = $this->app->get(Router::class);
        $router->post('/test', fn(ApiRequest $request) => response(200));

        $response = $this->postJson('/test', [
            'first_name' => 'foo',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'last_name' => ['The property last_name is required'],
                    'email' => ['The property email is required']
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_should_accept_valid_json()
    {
        $router = $this->app->get(Router::class);
        $router->post('/test', fn(ApiRequest $request) => $request->validated());

        $data = [
            'first_name' => 'foo',
            'last_name' => 'bar',
            'email' => 'foo@bar.com',
        ];

        $this->postJson('/test', $data)->assertOk()->assertJson($data);
    }

    public function it_should_not_resolve_the_validator_more_than_once()
    {
        $request = app(ApiRequest::class);
        $request->getValidatorInstance();
    }
}