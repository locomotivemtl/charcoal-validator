<?php

declare(strict_types=1);

namespace Charcoal\Validator;

/**
 *
 */
class Validator
{
    public const LEVEL_ERROR = 'error';
    public const LEVEL_WARNING = 'warning';
    public const LEVEL_INFO = 'info';

    /**
     * @var array<string, RuleInterface[]>
     */
    private $rules = [];

    /**
     * @param RuleInterface[] $errorRules
     * @param RuleInterface[] $warningRules
     * @param RuleInterface[] $infoRules
     */
    public function __construct(array $errorRules = [], array $warningRules = [], array $infoRules = [])
    {
        $this->rules = [];
        $this->addRules(self::LEVEL_ERROR, $errorRules);
        $this->addRules(self::LEVEL_WARNING, $warningRules);
        $this->addRules(self::LEVEL_INFO, $infoRules);
    }

    /**
     * @param mixed $val The value to validate.
     * @return Validation
     */
    public function __invoke($val): Validation
    {
        return $this->validate($val);
    }

    /**
     * @param mixed $val The value to validate.
     * @return Validation
     */
    public function validate($val): Validation
    {
        return new Validation(
            $this->processRules(self::LEVEL_ERROR, $val),
            $this->processRules(self::LEVEL_WARNING, $val),
            $this->processRules(self::LEVEL_INFO, $val)
        );
    }

    /**
     * @param string $level The rules level.
     * @param mixed $val The value to validate.
     * @return array<Result>
     */
    protected function processRules(string $level, $val): array
    {
        if (!isset($this->rules[$level])) {
            return [];
        }
        $results = [];
        foreach ($this->rules[$level] as $rule) {
            $results[] = $rule($val);
        }
        return $results;
    }

    /**
     * @param string $level The rules level.
     * @param RuleInterface[] $rules The rules.
     * @return void
     */
    private function addRules(string $level, array $rules): void
    {
        foreach ($rules as $rule) {
            $this->rules[$level][] = $rule;
        }
    }
}
