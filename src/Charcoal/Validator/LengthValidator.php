<?php

namespace Charcoal\Validator;

// Local depenencies
use Charcoal\Validator\Validator as AbstractValidator;

/**
 * Length validator ensures a string is of a certain length.
 *
 */
class LengthValidator extends AbstractValidator
{
    /**
     * Minimum length. 0 = no limit.
     *
     * @var integer $min
     */
    private $min = 0;

    /**
     * Maximum length. 0 = no limit.
     *
     * @var integer $max
     */
    private $max = 0;

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
            $this->min = intval($data['min']);
        }
        if (isset($data['max'])) {
            $this->max = intval($data['max']);
        }
        if (isset($data['unicode'])) {
            $this->unicode = !!$data['unicode'];
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {
        if (!$this->min && !$this->max) {
            return $this->skip($val, 'length.skipped.no-min-max');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null) {
            return $this->skip($val, 'length.skipped.empty-val');
        }

        // Non-string value should be handled by different validators.
        if (!is_scalar($val) && !(is_object($val) && method_exists($val, '__toString'))) {
            return $this->skip($val, 'length.skipped.invalid-type');
        }

        $val = (string)$val;

        if ($this->validateMin($val) === false) {
            return $this->failure($val, 'length.failure.min');
        }

        if ($this->validateMax($val) === false) {
            return $this->failure($val, 'length.failure.max');
        }

        return $this->success($val, 'length.success');
    }

    /**
     * @param string $val The string to validate length agains minimal constraint.
     * @return boolean
     */
    private function validateMin($val)
    {
        if ($this->min) {
            if ($this->unicode) {
                return (mb_strlen($val) >= $this->min);
            } else {
                return (strlen($val) >= $this->min);
            }
        } else {
            return true;
        }
    }

    /**
     * @param string $val The string to validate length against maximal constraint.
     * @return boolean
     */
    private function validateMax($val)
    {
        if ($this->max) {
            if ($this->unicode) {
                return (mb_strlen($val) <= $this->max);
            } else {
                return (strlen($val) <= $this->max);
            }
        } else {
            return true;
        }
    }

    /**
     * @return string[]
     */
    protected function messages()
    {
        return [
            'length.failure.min'            => sprintf('The value must be at least %s characters.', $this->min),
            'length.failure.max'            => sprintf('The value must be a maximum of %s characters.', $this->max),
            'length.skipped.no-min-max'     => 'Length validation skipped, no min or max defined.',
            'length.skipped.empty-val'      => 'Length validation skipped, value is empty.',
            'length.skipped.invalid-type'   => 'Length validation skipped, value not a string.',
            'length.success'                => sprintf('The value is between %s and %s characters.', $this->min, $this->max)
        ];
    }
}
