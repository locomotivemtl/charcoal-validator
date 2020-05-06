<?php

declare(strict_types=1);

namespace Charcoal\Validator;

use JsonSerializable;

class Validation implements JsonSerializable
{
    /**
     * @var array<string, Result[]>
     */
    private $results = [];

    /**
     * @param array<Result> $errorResults
     * @param array<Result> $warningResults
     * @param array<Result> $infoResults
     */
    public function __construct(array $errorResults = [], array $warningResults = [], array $infoResults = [])
    {
        $this->addResults(Validator::LEVEL_ERROR, $errorResults);
        $this->addResults(Validator::LEVEL_WARNING, $warningResults);
        $this->addResults(Validator::LEVEL_INFO, $infoResults);
    }

    /**
     * @param string|null $level
     * @return array<Result>|array<string, Result[]>
     */
    public function results(
        ?string $level = null,
        bool $returnFailures = true,
        bool $returnSkipped = false,
        bool $returnSuccess = false
    ): array {
        $filter = function (Result $var) use ($returnFailures, $returnSkipped, $returnSuccess): bool {
            if ($returnFailures === true && $var->getType() === Result::TYPE_FAILURE) {
                return true;
            }
            if ($returnSkipped === true && $var->getType() === Result::TYPE_SKIPPED) {
                return true;
            }
            if ($returnSuccess === true && $var->getType() === Result::TYPE_SUCCESS) {
                return true;
            }
            return false;
        };
        if ($level === null) {
            $ret = [];
            foreach ($this->results as $level => $results) {
                $ret[$level] = array_filter($results, $filter);
                if (empty($ret[$level])) {
                    unset($ret[$level]);
                }
            }
            return $ret;
        } else {
            return array_filter($this->results[$level], $filter);
        }
    }

    /**
     * @param bool $checkWarning
     * @param bool $checkInfo
     * @return bool
     */
    public function isValid(bool $checkWarning = false, bool $checkInfo = false): bool
    {
        if (isset($this->results[Validator::LEVEL_ERROR])) {
            foreach ($this->results[Validator::LEVEL_ERROR] as $result) {
                if ($result->isValid() === false) {
                    return false;
                }
            }
        }
        if ($checkWarning === true && isset($this->results[Validator::LEVEL_WARNING])) {
            foreach ($this->results[Validator::LEVEL_WARNING] as $result) {
                if ($result->isValid() === false) {
                    return false;
                }
            }
        }
        if ($checkInfo === true && isset($this->results[Validator::LEVEL_INFO])) {
            foreach ($this->results[Validator::LEVEL_INFO] as $result) {
                if ($result->isValid() === false) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @return array<string, array>
     */
    public function jsonSerialize(): array
    {
        return $this->results;
    }

    /**
     * @param string $level
     * @param array<Result> $results
     */
    private function addResults(string $level, array $results): void
    {
        foreach ($results as $result) {
            $this->addResult($level, $result);
        }
    }

    /**
     * @param string $level
     * @param Result $result
     */
    private function addResult(string $level, Result $result): void
    {
        $this->results[$level][] = $result;
    }
}
