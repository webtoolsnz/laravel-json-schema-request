<?php

namespace Webtools\JsonSchemaRequest;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\MessageBag;
use JsonSchema\Validator as SchemaValidator;
use JsonSchema\Constraints\Constraint;
use Webtools\JsonSchemaRequest\Exceptions\ValidationException;

class JsonSchemaValidator implements ValidatorContract
{
    protected SchemaValidator $validator;
    protected array $schema;
    protected array $data;
    protected ?MessageBag $failedConstraints = null;
    protected ?MessageBag $messages = null;

    /**
     * Array of callbacks to be executed after validation
     *
     * @var \Closure[]
     */
    private array $after = [];

    public function __construct(SchemaValidator $validator, array $schema, array $data)
    {
        $this->validator = $validator;
        $this->schema = $schema;
        $this->data = $data;
    }

    public function passes(): bool
    {
        $this->messages = new MessageBag();
        $this->failedConstraints = new MessageBag();

        $this->validator->validate($this->data, $this->schema, Constraint::CHECK_MODE_TYPE_CAST);

        foreach ($this->validator->getErrors(SchemaValidator::ERROR_DOCUMENT_VALIDATION) as $error) {
            $this->messages->add($error['property'], $error['message']);
            $this->failedConstraints->add($error['property'], $error['constraint']);
        }

        foreach ($this->after as $after) {
            $after();
        }

        return $this->messages->isEmpty();
    }

    public function getMessageBag()
    {
        return $this->messages;
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function validate()
    {
        if ($this->fails()) {
            $this->signalFailure();
        }

        return $this->validated();
    }

    /**
     * @inheritDoc
     */
    public function validated()
    {
        if (!$this->messages) {
            $this->passes();
        }

        if ($this->messages->isNotEmpty()) {
            $this->signalFailure();
        }

        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function fails()
    {
        return !$this->passes();
    }

    /**
     * Returns a list of the failed constraints for each property
     * @return array
     */
    public function failed()
    {
        return $this->failedConstraints->messages();
    }

    /**
     * This is a NO-OP, was only added to support the ValidatorContract,
     * Rules cannot be applied to a schema in this manner.
     */
    public function sometimes($attribute, $rules, callable $callback)
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function after($callback)
    {
        $this->after[] = function () use ($callback) {
            return call_user_func_array($callback, [$this]);
        };

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function errors()
    {
        return $this->getMessageBag();
    }

    private function signalFailure()
    {
        throw new ValidationException($this);
    }
}
