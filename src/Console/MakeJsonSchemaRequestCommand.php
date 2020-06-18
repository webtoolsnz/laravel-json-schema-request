<?php

namespace Webtools\JsonSchemaRequest\Console;

use Illuminate\Console\GeneratorCommand;

class MakeJsonSchemaRequestCommand extends GeneratorCommand
{
    protected $name = 'make:json-request';

    protected $description = 'Create a new JSON Schema Request';

    protected $type = 'JsonSchemaRequest';

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/json-schema-request.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Requests';
    }
}