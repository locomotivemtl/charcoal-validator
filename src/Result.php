<?php

declare(strict_types=1);

namespace Charcoal\Validator;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use JsonSerializable;

/**
 *
 */
class Result implements JsonSerializable
{
    public const TYPE_SUCCESS = 'success';
    public const TYPE_FAILURE = 'failure';
    public const TYPE_SKIPPED = 'skipped';

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
     * @param string $type
     * @param string $code
     * @param string $message
     */
    public function __construct(string $type, string $code, string $message)
    {
        $this->type = $type;
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return ($this->type !== self::TYPE_FAILURE);
    }

    /**
     * @return bool
     */
    public function isSkipped(): bool
    {
        return ($this->type === self::TYPE_SKIPPED);
    }

    /**
     * JsonSerializable::jsonSerialize()
     *
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'code' => $this->code,
            'message' => $this->message
        ];
    }
}
