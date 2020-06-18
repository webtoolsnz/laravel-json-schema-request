<?php

namespace Webtools\JsonSchemaRequest;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidatesWhenResolvedTrait;

class JsonSchemaRequest extends Request implements ValidatesWhenResolved
{
    use ValidatesWhenResolvedTrait;

    protected Container $container;

    protected ?ValidatorContract $validator = null;

    public function getValidatorInstance()
    {
        if (!$this->validator) {
            $this->validator = new JsonSchemaValidator(
                $this->container->make(\JsonSchema\Validator::class),
                $this->container->call([$this, 'schema']),
                $this->json()->all(),
            );
        }

        return $this->validator;
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    public function failedValidation(JsonSchemaValidator $validator)
    {
        throw new HttpResponseException(response()->json(
            ['errors' => $validator->errors()],
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));
    }

    public function validated()
    {
        return $this->validator->validated();
    }
}
