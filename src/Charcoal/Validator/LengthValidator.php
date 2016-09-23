<?php

namespace Charcoal\Validator;

// PHP dependencies
use \InvalidArgumentException;

// Local depenencies
use \Charcoal\Validator\AbstractValidator;
use \Charcoal\Validator\ValidationResult;

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
            $this->setMin($data['min']);
        }
        if (isset($data['max'])) {
            $this->setMax($data['max']);
        }
        if (isset($data['unicode'])) {
            $this->setUnicode($data['unicode']);
        }
    }

    /**
     * @param integer $min The minimum allowed length. (0 = no limit).
     * @return void
     */
    private function setMin($min)
    {
        $this->min = (int)$min;
    }

    /**
     * @return integer The minimum length.
     */
    private function min()
    {
        return $this->min;
    }

    /**
     * @param boolean $max The allow-alpha flag.
     * @return void
     */
    private function setMax($max)
    {
        $this->max = (int)$max;
    }

    /**
     * @return boolean
     */
    private function max()
    {
        return $this->max;
    }

    /**
     * @param string $unicode The unicode (multibytes) flag.
     * @return  void
     */
    private function setUnicode($unicode)
    {
        $this->unicode = !!$unicode;
    }

    /**
     * @return string
     */
    private function unicode()
    {
        return $this->unicode;
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {
        if (!$this->min() && !$this->max()) {
            return $this->skip($val, 'length.skipped.no-min-max');
        }

        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'length.skipped.empty-val');
        }

        if (!is_scalar($val) && !(is_object($val) && method_exists($val, '__toString'))) {
            return $this->skip($val, 'length.skipped.invalid-type');
        }

        $val = (string)$val;

        if ($this->min()) {
            if ($this->unicode()) {
                $valid = (mb_strlen($val) >= $this->min());
            } else {
                $valid = (strlen($val) >= $this->min());
            }
            if (!$valid) {
                return $this->failure($val, 'length.min');
            }
        }

        if ($this->max()) {
            if ($this->unicode()) {
                $valid = (mb_strlen($val) <= $this->max());
            } else {
                $valid = (strlen($val) <= $this->max());
            }
            if (!$valid) {
                return $this->failure($val, 'length.max');
            }
        }

        return $this->success($val, 'length.success');
    }

    /**
     * @return string[]
     */
    protected function messages()
    {
        return [
            'length.failure.min'            => sprintf('The value must be at least %s characters.', $this->min()),
            'length.failure.max'            => sprintf('The value must be a maximum of %s characters.', $this->min()),
            'length.skipped.no-min-max'     => 'Length validation skipped, no min or max defined.',
            'length.skipped.empty-val'      => 'Length validation skipped, value is empty.',
            'length.skipped.invalid-type'   => 'Length validation skipped, value not a string.',
            'length.success'   => sprintf('The value is between %s and %s characters.', $this->min(), $this->max())
        ];
    }
}
