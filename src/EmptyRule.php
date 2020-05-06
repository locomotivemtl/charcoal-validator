<?php

declare(strict_types=1);

namespace Charcoal\Validator;

/**
 * Ensures a value is not empty.
 * Can also ensures a value *is* empty with `require_empty` set to true.
 */
class EmptyRule extends Rule
{
    /**
     * @var bool
     */
    private $requireEmpty = false;

    /**
     * @param array<string, mixed> $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['requireEmpty'])) {
            $this->requireEmpty = !!$data['requireEmpty'];
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return Result
     */
    public function process($val): Result
    {
        if ($this->requireEmpty) {
            return $this->validateIsEmpty($val);
        } else {
            return $this->validateIsNotEmpty($val);
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return Result
     */
    protected function validateIsEmpty($val): Result
    {
        if (!empty($val)) {
            return $this->failure('empty.failure.is-not-empty');
        } else {
            return $this->success('empty.success.is-empty');
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return Result
     */
    protected function validateIsNotEmpty($val): Result
    {
        if (empty($val)) {
            return $this->failure('empty.failure.is-empty');
        } else {
            return $this->success('empty.success.is-not-empty');
        }
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'empty.failure.is-empty' => 'The value can not be empty.',
            'empty.failure.is-not-empty' => 'The value must be empty.',
            'empty.success.is-not-empty' => 'The value is not empty.',
            'empty.success.is-empty' => 'The value is empty'
        ];
    }
}
