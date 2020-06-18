<?php

namespace Webtools\JsonSchemaRequest\Tests;

use Webtools\JsonSchemaRequest\Exceptions\ValidationException;
use Webtools\JsonSchemaRequest\JsonSchemaValidator;
use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;

class JsonSchemaValidatorTest extends TestCase
{
    protected array $schema = [
        'type' => 'object',
        'properties' => [
            'first_name' => ['type' => 'string'],
            'last_name' => ['type' => 'string'],
            'email' => ['type' => 'string', 'format' => 'email']
        ],
        'required' => ['first_name', 'last_name'],
        'additionalProperties' => false,
    ];

    protected array $validPayload = [
        'first_name' => 'Joe',
        'last_name' => 'Bloggs',
        'email' => 'foo@bar.com'
    ];

    /**
     * @test
     */
    public function it_should_throw_a_http_exception_on_failure()
    {
        $validator = new JsonSchemaValidator(new Validator(), $this->schema, [
            'first_name' => 'Joe',
            'email' => 'my-email'
        ]);

        $exception = null;

        try {
            $validator->validate();
        } catch (ValidationException $exception) {
            // If this is caught $exception will be assigned.
        }

        $this->assertNotNull($exception);
    }

    /**
     * @test
     */
    public function it_should_accept_valid_data()
    {
        $validator = new JsonSchemaValidator(new Validator(), $this->schema, $this->validPayload);
        $this->assertEquals($this->validPayload, $validator->validate());
        $this->assertEmpty($validator->errors());
    }

    /**
     * @test
     */
    public function it_supports_after_validation_hooks()
    {
        $validator = new JsonSchemaValidator(new Validator(), $this->schema, $this->validPayload);

        $called = false;
        $validator->after(function () use (&$called) {
            $called = true;
        });

        $validator->validate();
        $this->assertTrue($called);
    }

    /**
     * @test
     */
    public function calling_validated_should_perform_validation_if_not_already_done()
    {
        $validator = new JsonSchemaValidator(new Validator(), $this->schema, []);

        $this->expectException(ValidationException::class);
        $validator->validated();
    }

    /**
     * @test
     */
    public function somtimes_is_a_no_op()
    {
        $validator = new JsonSchemaValidator(new Validator(), $this->schema, $this->validPayload);

        $neverCalled = true;

        $validator->sometimes('foo', 'bar', function () use(&$neverCalled) {
            $neverCalled = false;
        });

        $validator->validated();
        $this->assertTrue($neverCalled);
    }

    /**
     * @test
     */
    public function it_should_return_a_list_of_failed_constraints()
    {
        $validator = new JsonSchemaValidator(new Validator(), $this->schema, [
            'first_name' => null,
            'email' => 'foo',
            'testing' => 123,
        ]);

        try {
            $validator->validate();
        } catch (ValidationException $exception) {
            //
        }

        $this->assertEquals([
            'first_name' => ['type'],
            'last_name' => ['required'],
            'email' => ['format'],
            '' => ['additionalProp'],
        ], $validator->failed());

    }
}
