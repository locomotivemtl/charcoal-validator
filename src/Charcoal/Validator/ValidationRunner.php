<?php

namespace Charcoal\Validator;

use \Charcoal\Validator\ValidatorInterface;

/**
 *
 */
class ValidationRunner
{

    const LEVEL_ERROR   = 'errors';
    const LEVEL_WARNING = 'warnings';
    const LEVEL_INFO    = 'infos';

    /**
     * The validators set on the runner.
     *
     * @var ValidatorInterface[]
     */
    private $validators;

    /**
     * Results of the validation.
     *
     * @var array
     */
    private $results;

    /**
     * @param array $data The constructor data, containing the validators.
     */
    public function __construct(array $data)
    {
        if (isset($data[self::LEVEL_ERROR])) {
            $this->addValidators(self::LEVEL_ERROR, $data[self::LEVEL_ERROR]);
        }
        if (isset($data[self::LEVEL_WARNING])) {
            $this->addValidators(self::LEVEL_WARNING, $data[self::LEVEL_WARNING]);
        }
        if (isset($data[self::LEVEL_INFO])) {
            $this->addValidators(self::LEVEL_INFO, $data[self::LEVEL_INFO]);
        }
    }

    /**
     * @param mixed   $val           The value to validate.
     * @param boolean $returnSkipped If false (default), the skipped results will be ignored. If true, they will be returned.
     * @param boolean $returnValid   If false (default), the valid results will be ignored. If true, they will be returned.
     * @return array
     */
    public function validate($val, $returnSkipped = false, $returnValid = false)
    {
        // Reset results.
        $this->results = [];

        $this->runValidators(self::LEVEL_ERROR, $val);
        $this->runValidators(self::LEVEL_WARNING, $val);
        $this->runValidators(self::LEVEL_INFO, $val);

        return $this->results($returnSkipped, $returnValid);
    }

    /**
     * @param boolean $returnSkipped If false (default), the skipped results will be ignored. If true, they will be returned.
     * @param boolean $returnValid   If false (default), the valid results will be ignored. If true, they will be returned.
     * @return ValidationResult[]
     */
    public function results($returnSkipped = false, $returnValid = false)
    {
        $ret = [];
        foreach ($this->results as $level => $results) {
            $ret[$level] = $this->parseResults($results, $returnSkipped, $returnValid);
        }
        return $ret;
    }

    /**
     * @param boolean $returnSkipped If false (default), the skipped results will be ignored. If true, they will be returned.
     * @param boolean $returnValid   If false (default), the valid results will be ignored. If true, they will be returned.
     * @return ValidationResult[]
     */
    public function errors($returnSkipped = false, $returnValid = false)
    {
        if (!isset($this->results[self::LEVEL_ERROR])) {
            return [];
        }
        return $this->parseResults($this->results[self::LEVEL_ERROR], $returnSkipped, $returnValid);
    }

    /**
     * @param boolean $returnSkipped If false (default), the skipped results will be ignored. If true, they will be returned.
     * @param boolean $returnValid   If false (default), the valid results will be ignored. If true, they will be returned.
     * @return ValidationResult[]
     */
    public function warnings($returnSkipped = false, $returnValid = false)
    {
        if (!isset($this->results[self::LEVEL_WARNING])) {
            return [];
        }
        return $this->parseResults($this->results[self::LEVEL_WARNING], $returnSkipped, $returnValid);
    }

    /**
     * @param boolean $returnSkipped If false (default), the skipped results will be ignored. If true, they will be returned.
     * @param boolean $returnValid   If false (default), the valid results will be ignored. If true, they will be returned.
     * @return ValidationResult[]
     */
    public function infos($returnSkipped = false, $returnValid = false)
    {
        if (!isset($this->results[self::LEVEL_INFO])) {
            return [];
        }
        return $this->parseResults($this->results[self::LEVEL_INFO], $returnSkipped, $returnValid);
    }

    /**
     * @param string $level The level to check.
     * @return boolean
     */
    public function isValid($level = null)
    {
        if ($level === null) {
            return $this->allLevelsValid();
        } else {
            return $this->isLevelValid($level);
        }
    }

    /**
     * @param ValidationResult[] $results       The results to parse.
     * @param boolean            $returnSkipped If false (default), the skipped results will be ignored. If true, they will be returned.
     * @param boolean            $returnValid   If false (default), the valid results will be ignored. If true, they will be returned.
     * @return ValidationResult[]
     */
    protected function parseResults(array $results, $returnSkipped = false, $returnValid = false)
    {
        $ret = [];
        foreach ($results as $result) {
            $dontContinue = false;
            if (!$returnSkipped && $result->isSkipped()) {
                continue;
            } else {
                $dontContinue = $result->isSkipped();
            }
            if ($dontContinue === false && !$returnValid && $result->isValid()) {
                continue;
            }

            $ret[] = $result;
        }
        return $ret;
    }

    /**
     * @param string               $level      The validators level.
     * @param ValidatorInterface[] $validators The validators.
     * @return void
     */
    protected function addValidators($level, array $validators)
    {
        foreach ($validators as $validator) {
            $this->addValidator($level, $validator);
        }
    }

    /**
     * @param string             $level     The validator level.
     * @param ValidatorInterface $validator The validator.
     * @return void
     */
    protected function addValidator($level, ValidatorInterface $validator)
    {
        $this->validators[$level][] = $validator;
    }

    /**
     * @param string $level The validators level.
     * @param mixed  $val   The value to validate.
     * @return void
     */
    protected function runValidators($level, $val)
    {
        if (!isset($this->validators[$level])) {
            return;
        }

        if (!isset($this->results[$level])) {
            $this->results[$level] = [];
        }

        foreach ($this->validators[$level] as $validator) {
            $res = $validator($val);
            $this->results[$level][$res->code()] = $res;
        }
    }

    /**
     * @param string $level The leve to check.
     * @return boolean
     */
    private function isLevelValid($level)
    {
        if (!isset($this->results[$level])) {
            return true;
        }
        foreach ($this->results[$level] as $result) {
            if (!$result->isValid()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return boolean
     */
    private function allLevelsValid()
    {
        foreach ($this->results as $level => $results) {
            foreach ($results as $result) {
                if (!$result->isValid()) {
                    return false;
                }
            }
        }
        return true;
    }
}
