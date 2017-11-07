<?php

namespace Charcoal\Validator;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use JsonSerializable;

/**
 *
 */
class ValidationResult implements JsonSerializable
{
    const TYPE_SUCCESS = 'success';
    const TYPE_FAILURE = 'failure';
    const TYPE_SKIPPED = 'skipped';

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @var DateTimeInterface
     */
    private $ts;

    /**
     * @param array $data The result data.
     */
    public function __construct(array $data)
    {
        $this->setTs('now');
        $this->setData($data);
    }

    /**
     * @return string
     */
    public function code()
    {
        return $this->code;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return ($this->type !== self::TYPE_FAILURE);
    }

    /**
     * @return boolean
     */
    public function isSkipped()
    {
        return ($this->type === self::TYPE_SKIPPED);
    }

    /**
     * JsonSerializable::jsonSerialize()
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->serializeData();
    }

    /**
     * @param array $data The constructor data.
     * @return void
     */
    private function setData(array $data)
    {
        if (isset($data['value'])) {
            $this->value = $data['value'];
        }
        if (isset($data['type'])) {
            $this->type = (string)$data['type'];
        }
        if (isset($data['code'])) {
            $this->code = (string)$data['code'];
        }
        if (isset($data['message'])) {
            $this->message = (string)$data['message'];
        }
        if (isset($data['ts'])) {
            $this->setTs($data['ts']);
        }
    }

    /**
     * @return array
     */
    private function serializeData()
    {
        return [
            'value'     => $this->value,
            'type'      => $this->type,
            'code'      => $this->code,
            'message'   => $this->message,
            'ts'        => $this->ts->format('Y-m-d H:i:s')
        ];
    }

    /**
     * @param DateTimeInterface|string|null $ts The date/time of the validation.
     * @throws InvalidArgumentException If the ts argument is not a valid datetime object or string.
     * @return void
     */
    private function setTs($ts)
    {
        if (is_string($ts)) {
            $ts = new DateTime($ts);
        }
        if (!($ts instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid "ts" value. Must be a date/time string or a DateTime object.'
            );
        }
        $this->ts = $ts;
    }
}
