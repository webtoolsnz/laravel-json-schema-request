JSON Schema Request
================================
[![CI Action](https://github.com/webtoolsnz/laravel-json-schema-request/workflows/continuous-integration/badge.svg)](https://github.com/webtoolsnz/laravel-json-schema-request/workflows/continuous-integration)
[![Code Coverage](https://scrutinizer-ci.com/g/webtoolsnz/laravel-json-schema-request/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/webtoolsnz/laravel-json-schema-request/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webtoolsnz/laravel-json-schema-request/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webtoolsnz/laravel-json-schema-request/?branch=master)

Laravels [Form Request Validation](https://laravel.com/docs/7.x/validation#form-request-validation) for JSON Schema documents  
 
Installation
--------------

```bash
 composer require webtoolsnz/laravel-json-schema-request
```

Usage
------
The development experience is identical to Laravel's Form Request Validation, except instead of writing Laravel validation rules, you write a [JSON Schema](https://json-schema.org/). 

You can create a new request using the `make:json-request` command

```bash
artisan make:json-request MyJsonRequest
``` 

You will now have new request class `App\Http\Requests\MyJsonRequest`, Below you can see a basic example schema.

```php
<?php

namespace App\Http\Requests;

use Webtools\JsonSchemaRequest\JsonSchemaRequest;

class MyJsonRequest extends JsonSchemaRequest
{
    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'first_name' => ['type' => 'string'],
                'last_name' => ['type' => 'string'],
                'email' => ['type' => 'string', 'format' => 'email'],
            ],
            'required' => ['first_name', 'last_name', 'email'],
            'additionalProperties' => false,
        ];
    }
}
```

Once you have a `JsonSchemaRequest` object, all you need to do is type-hint the request on your controller method. 
The incoming form request is validated before the controller method is called.

```php
public function store(MyJsonRequest $request)
{
    // The incoming request is valid...

    // Retrieve the validated input data...
    $validated = $request->validated();
}
```

License
-------
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.