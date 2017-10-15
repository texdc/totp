<?php
/**
 * AssertionConcern.php
 *
 * @license   http://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @copyright 2017 George D. Cooksey, III
 */

namespace texdc\totp\assert;

use Assert\AssertionChain;
use ReflectionClass;

class AssertionConcern extends AssertionChain
{
    private static $assertionClass = Assertion::class;

    private $value;
    private $defaultMessage;
    private $defaultPropertyPath;

    /**
     * Return each assertion as always valid.
     *
     * @var bool
     */
    private $alwaysValid = false;

    /**
     * Perform assertion on every element of array or traversable.
     *
     * @var bool
     */
    private $all = false;

    /**
     * @var ReflectionClass
     */
    private $reflClass;

    /**
     * Constructor
     *
     * @param mixed       $value
     * @param string|null $defaultMessage
     * @param string|null $defaultPropertyPath
     */
    public function __construct($value, string $defaultMessage = null, string $defaultPropertyPath = null)
    {
        $this->value = $value;
        $this->defaultMessage = $defaultMessage;
        $this->defaultPropertyPath = $defaultPropertyPath;
        $this->reflClass = new ReflectionClass(static::$assertionClass);
    }

    /**
     * Call an assertion on the current value.
     *
     * @param string $methodName
     * @param array  $args
     *
     * @return AssertionConcern
     */
    public function __call($methodName, $args)
    {
        if ($this->alwaysValid === true) {
            return $this;
        }

        if (!$this->reflClass->hasMethod($methodName)) {
            throw new \RuntimeException("Assertion '" . $methodName . "' does not exist.");
        }

        $args = $this->prepareArgs($methodName, $args);

        if ($this->all) {
            $methodName = 'all' . $methodName;
        }

        call_user_func_array([static::$assertionClass, $methodName], $args);

        return $this;
    }

    /**
     * Switch chain into validation mode for an array of values.
     *
     * @return AssertionConcern
     */
    public function all() : self
    {
        $this->all = true;

        return $this;
    }

    /**
     * Switch chain into mode allowing nulls, ignoring further assertions.
     *
     * @return AssertionConcern
     */
    public function nullOr() : self
    {
        if ($this->value === null) {
            $this->alwaysValid = true;
        }

        return $this;
    }

    /**
     * @param  string $methodName
     * @param  array  $args
     * @return array
     */
    private function prepareArgs(string $methodName, array $args) : array
    {
        $params = $this->getMethodParameters($methodName);
        array_unshift($args, $this->value);

        foreach ($params as $idx => $param) {
            if (isset($args[$idx])) {
                continue;
            }

            if ($param->getName() == 'message') {
                $args[$idx] = $this->defaultMessage;
            }

            if ($param->getName() == 'propertyPath') {
                $args[$idx] = $this->defaultPropertyPath;
            }
        }

        return $args;
    }

    /**
     * @param  string $methodName
     * @return array
     */
    private function getMethodParameters(string $methodName) : array
    {
        $method = $this->reflClass->getMethod($methodName);
        return $method->getParameters();
    }
}
