<?php

declare(strict_types=1);

namespace Charcoal\Validator;

// PHP dependencies
use _HumbugBox09702017065e\Nette\Utils\Paginator;
use InvalidArgumentException;

/**
 * Number validator ensures a number is within a certain range (min, max).
 *
 */
class NumberRule extends Rule
{
    /**
     * Minimum length. 0 = no limit.
     *
     * @var mixed|null $min
     */
    private $min = null;

    /**
     * Maximum length. 0 = no limit.
     *
     * @var mixed|null $max
     */
    private $max = null;

    /**
     * Whether to return failure or skipped on type validation.
     *
     * @var bool
     */
    private $checkType = true;

    /**
     * @param array<string, mixed> $data Constructor data.
     * @throws InvalidArgumentException If the min or max argument is not numeric.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['min'])) {
            if (!is_numeric($data['min'])) {
                throw new InvalidArgumentException(
                    'Can not set min: not a numerical value.'
                );
            }
            $this->min = $data['min'];
        }
        if (isset($data['max'])) {
            if (!is_numeric($data['max'])) {
                throw new InvalidArgumentException(
                    'Can not set max: not a numerical value.'
                );
            }
            $this->max = $data['max'];
        }
        if (isset($data['checkType'])) {
            $this->checkType = !!$data['checkType'];
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return Result
     */
    public function process($val): Result
    {
        if (!$this->min && !$this->max) {
            return $this->skip('number.skipped.no-min-max');
        }

        // Null values and empty strings should be handled by different validators (NullValidator and EmptyValidator)
        if ($val === null || $val === '') {
            return $this->skip('number.skipped.empty-val');
        }

        if (!is_numeric($val)) {
            if ($this->checkType === true) {
                return $this->failure('number.failure.invalid-type');
            }
            return $this->skip('number.skipped.invalid-type');
        }


        if ($this->min) {
            $valid = $val >= $this->min;
            if (!$valid) {
                return $this->failure('number.failure.min');
            }
        }

        if ($this->max) {
            $valid = $val <= $this->max;
            if (!$valid) {
                return $this->failure('number.failure.max');
            }
        }

        return $this->success('number.success');
    }

    /**
     * @return string[]
     */
    protected function messages(): array
    {
        return [
            'number.failure.invalid-type' => 'The number is not valid.',
            'number.failure.min' => sprintf('The number must be greater than "%s".', $this->min),
            'number.failure.max' => sprintf('The number must be lower "%s".', $this->max),
            'number.skipped.no-min-max' => 'Number validation skipped, no min or max defined.',
            'number.skipped.empty-val' => 'Number validation skipped, value is empty.',
            'number.skipped.invalid-type' => 'Number validation skipped, value not numeric.',
            'number.success' => 'The number is valid.'
        ];
    }
}
