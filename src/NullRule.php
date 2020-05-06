<?php

declare(strict_types=1);

namespace Charcoal\Validator;

/**
 * Ensures a value is not null.
 * Can also ensures a value *is* null with `require_null` set to true.
 */
class NullRule extends Rule
{
    /**
     * @var boolean
     */
    private $requireNull = false;

    /**
     * @param array<string, mixed> $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['requireNull'])) {
            $this->requireNull = !!$data['requireNull'];
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return Result
     */
    public function process($val): Result
    {
        if ($this->requireNull) {
            return $this->validateIsNull($val);
        } else {
            return $this->validateIsNotNull($val);
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return Result
     */
    protected function validateIsNull($val): Result
    {
        if ($val !== null) {
            return $this->failure('null.failure.is-not-null');
        } else {
            return $this->success('null.success.is-null');
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return Result
     */
    protected function validateIsNotNull($val): Result
    {
        if ($val === null) {
            return $this->failure('null.failure.is-null');
        } else {
            return $this->success('null.success.is-not-null');
        }
    }

    /**
     * @return array<string,string>
     */
    protected function messages(): array
    {
        return [
            'null.failure.is-null' => 'The value can not be null.',
            'null.failure.is-not-null' => 'The value must be null.',
            'null.success.is-not-null' => 'The value is not null.',
            'null.success.is-null' => 'The value is null'
        ];
    }
}
