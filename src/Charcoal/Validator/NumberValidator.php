<?php

namespace Charcoal\Validator;

// PHP dependencies
use Exception;
use InvalidArgumentException;
use DateTimeInterface;
use DateTime;

// Local depenencies
use Charcoal\Validator\AbstractValidator;
use Charcoal\Validator\ValidationResult;

/**
 * Number validator ensures a number is within a certain range (min, max).
 *
 */
class NumberValidator extends AbstractValidator
{
    /**
     * Minimum length. 0 = no limit.
     *
     * @var DateTimeInterface|null $min
     */
    private $min = null;

    /**
     * Maximum length. 0 = no limit.
     *
     * @var integer $max
     */
    private $max = null;

    /**
     * Count unicode (multibyes) characters as a single character.
     *
     * @var boolean $unicode
     */
    private $unicode = true;

    /**
     * @param array $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['min'])) {
            $this->setMin($data['min']);
        }
        if (isset($data['max'])) {
            $this->setMax($data['max']);
        }
    }

    /**
     * @param mixed|null $min The minimum allowed value.
     * @throws InvalidArgumentException If the min value invalid (not numeric).
     * @return self
     */
    private function setMin($min)
    {
        if (!is_numeric($min)) {
            throw new InvalidArgumentException(
                'Can not set min: not a numerical value.'
            );
        }
        $this->min = $min;
        return $this;
    }

    /**
     *  Retrieves the minimum (numeric) length.
     *
     * @return mixed|null
     */
    private function min()
    {
        return $this->min;
    }

    /**
     * @param mixed|null $max The maximum allowed value.
     * @throws InvalidArgumentException If the max value is invalid (not numeric).
     * @return self
     */
    private function setMax($max)
    {
        if (!is_numeric($max)) {
            throw new InvalidArgumentException(
                'Can not set max: not a numerical value.'
            );
        }
        $this->max = $max;
        return $this;
    }

    /**
     * Retrieves the maximum (numeric) length.
     *
     * @return mixed|null
     */
    private function max()
    {
        return $this->max;
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {
        if (!$this->min() && !$this->max()) {
            return $this->skip($val, 'number.skipped.no-min-max');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'number.skipped.empty-val');
        }

        if (!is_numeric($val)) {
            return $this->skip($val, 'number.skipped.invalid-type');
        }


        if ($this->min()) {
            $valid = $val >= $this->min();
            if (!$valid) {
                return $this->failure($val, 'number.failure.min');
            }
        }

        if ($this->max()) {
            $valid = $val <= $this->max();
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
            'number.failure.min'            => sprintf('The number must be greater than "%s".', $this->min()),
            'number.failure.max'            => sprintf('The number must be lower "%s".', $this->max()),
            'number.skipped.no-min-max'     => 'Number validation skipped, no min or max defined.',
            'number.skipped.empty-val'      => 'Number validation skipped, value is empty.',
            'number.skipped.invalid-type'   => 'Number validation skipped, value not numeric.',
            'number.success'                => 'The number is valid.'
        ];
    }
}
