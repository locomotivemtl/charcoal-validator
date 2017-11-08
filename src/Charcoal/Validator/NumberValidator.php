<?php

namespace Charcoal\Validator;

// PHP dependencies
use InvalidArgumentException;

// Local depenencies
use Charcoal\Validator\Validator as AbstractValidator;

/**
 * Number validator ensures a number is within a certain range (min, max).
 *
 */
class NumberValidator extends AbstractValidator
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
     * @param array $data Constructor data.
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
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {
        if (!$this->min && !$this->max) {
            return $this->skip($val, 'number.skipped.no-min-max');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'number.skipped.empty-val');
        }

        if (!is_numeric($val)) {
            return $this->skip($val, 'number.skipped.invalid-type');
        }


        if ($this->min) {
            $valid = $val >= $this->min;
            if (!$valid) {
                return $this->failure($val, 'number.failure.min');
            }
        }

        if ($this->max) {
            $valid = $val <= $this->max;
            if (!$valid) {
                return $this->failure($val, 'number.failure.max');
            }
        }

        return $this->success($val, 'number.success');
    }

    /**
     * @return string[]
     */
    protected function messages()
    {
        return [
            'number.failure.min'            => sprintf('The number must be greater than "%s".', $this->min),
            'number.failure.max'            => sprintf('The number must be lower "%s".', $this->max),
            'number.skipped.no-min-max'     => 'Number validation skipped, no min or max defined.',
            'number.skipped.empty-val'      => 'Number validation skipped, value is empty.',
            'number.skipped.invalid-type'   => 'Number validation skipped, value not numeric.',
            'number.success'                => 'The number is valid.'
        ];
    }
}
