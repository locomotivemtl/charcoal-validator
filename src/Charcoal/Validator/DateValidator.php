<?php

namespace Charcoal\Validator;

// PHP dependencies
use Exception;
use InvalidArgumentException;
use DateTimeInterface;
use DateTime;

// Local depenencies
use Charcoal\Validator\Validator as AbstractValidator;

/**
 * Length validator ensures a string is of a certain length.
 *
 */
class DateValidator extends AbstractValidator
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
     * @var DateTimeInterface|null $max
     */
    private $max = null;

    /**
     * Whether to return failure or skipped on type validation.
     *
     * @var boolean
     */
    private $checkType = true;

    /**
     * @param array $data Constructor data.
     */
    public function __construct(array $data = [])
    {
        if (isset($data['min'])) {
            $this->min = $this->parseDate($data['min']);
        }
        if (isset($data['max'])) {
            $this->max = $this->parseDate($data['max']);
        }
        if (isset($data['check_type'])) {
            $this->checkType = !!$data['check_type'];
        }
    }

    /**
     * @param mixed $val The value to validate.
     * @return ValidationResult
     */
    public function validate($val)
    {
        // Null values and empty strings should be handled by different validators.
        if ($val === null || $val === '') {
            return $this->skip($val, 'date.skipped.empty-val');
        }

        try {
            $val = $this->parseDate($val);
        } catch (Exception $e) {
            if ($this->checkType === true) {
                return $this->failure($val, 'date.failure.invalid-type');
            } else {
                return $this->skip($val, 'date.skipped.invalid-type');
            }
        }

        if ($this->min) {
            $valid = $val >= $this->min;
            if (!$valid) {
                return $this->failure($val, 'date.failure.min');
            }
        }

        if ($this->max) {
            $valid = $val <= $this->max;
            if (!$valid) {
                return $this->failure($val, 'date.failure.max');
            }
        }

        return $this->success($val, 'date.success');
    }

    /**
     * @param mixed $val The value to parse into a date.
     * @throws InvalidArgumentException If the value can not be turned into a date object (if it isn't one already).
     * @return DateTimeInterface
     */
    private function parseDate($val)
    {
        if (is_string($val)) {
            try {
                return new DateTime($val);
            } catch (Exception $e) {
                throw new InvalidArgumentException(
                    'String is not a valid date.'
                );
            }
        }
        if (!($val instanceof DateTimeInterface)) {
            throw new InvalidArgumentException('Invalid date.');
        }

        // If here, val is a DateTimeInterface.
        return $val;
    }

    /**
     * @return string[]
     */
    protected function messages()
    {
        $formattedMin = ($this->min !== null) ? $this->min->format('Y-m-d H:i:s'): '-';
        $formattedMax = ($this->max !== null) ? $this->max->format('Y-m-d H:i;s') : '-';
        return [
            'date.failure.invalid-type'   => 'The date is not valid.',
            'date.failure.min'            => sprintf('The date must be after "%s".', $formattedMin),
            'date.failure.max'            => sprintf('The date must be before "%s".', $formattedMax),
            'date.skipped.invalid-type'   => 'Date validation skipped, value is not valid.',
            'date.skipped.empty-val'      => 'Date validation skipped, value is empty.',
            'date.success'                => 'The date is valid.'
        ];
    }
}
