<?php
/**
 * Assertion.php
 *
 * @license   http://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @copyright 2017 George D. Cooksey, III
 */

namespace texdc\totp\assert;

use Assert\Assertion as BaseAssertion;

/**
 * Adds missing validations
 *
 * @method static void isModulus($value, $modulo, $message = null, $propertyPath = null)
 * @method static void notModulus($value, $modulo, $message = null, $propertyPath = null)
 * @method static void integerRange($value, $min, $max, $message = null, $propertyPath = null)
 * @method static void notIntegerRange($value, $min, $max, $message = null, $propertyPath = null)
 * @method static void notContains($value, $needle, $message = null, $propertyPath = null)
 */
class Assertion extends BaseAssertion
{
    const INVALID_MODULUS       = 400;
    const INVALID_NUMERIC_RANGE = 401;

    /**
     * Assert that a value is a modulus of $modulo
     *
     * @param  numeric     $value
     * @param  numeric     $modulo
     * @param  string|null $message
     * @param  string|null $propertyPath
     * @return void
     * @throws \Assert\AssertionFailedException
     */
    public static function isModulus($value, $modulo, $message = null, $propertyPath = null)
    {
        static::numeric($value);
        static::numeric($modulo);

        if ($value % $modulo !== 0) {
            $message = sprintf(
                $message ?: 'Value %d is not a modulus of %d',
                static::stringify($value),
                static::stringify($modulo)
            );

            throw static::createException($value, $message, static::INVALID_MODULUS, $propertyPath);
        }
    }

    /**
     * Assert that a value is not a modulus of $modulo
     *
     * @param  numeric     $value
     * @param  numeric     $modulo
     * @param  string|null $message
     * @param  string|null $propertyPath
     * @return void
     * @throws \Assert\AssertionFailedException
     */
    public static function notModulus($value, $modulo, $message = null, $propertyPath = null)
    {
        static::numeric($value);
        static::numeric($modulo);

        if ($value % $modulo === 0) {
            $message = sprintf(
                $message ?: 'Value %d is a modulus of %d',
                static::stringify($value),
                static::stringify($modulo)
            );

            throw static::createException($value, $message, static::INVALID_MODULUS, $propertyPath);
        }
    }

    /**
     * Assert that a value is between $min and $max
     *
     * @param  numeric     $value
     * @param  numeric     $min
     * @param  numeric     $max
     * @param  string|null $message
     * @param  string|null $propertyPath
     * @return void
     * @throws \Assert\AssertionFailedException
     */
    public static function numericRange($value, $min, $max, $message = null, $propertyPath = null)
    {
        static::numeric($value);
        static::numeric($min);
        static::numeric($max);

        if ($value < $min || $value > $max) {
            $message = sprintf(
                $message ?: 'Value %d is not between %d and %d',
                static::stringify($value),
                static::stringify($min),
                static::stringify($max)
            );

            throw static::createException($value, $message, static::INVALID_NUMERIC_RANGE, $propertyPath);
        }
    }

    /**
     * Assert that a value is not between $min and $max
     *
     * @param  numeric     $value
     * @param  numeric     $min
     * @param  numeric     $max
     * @param  string|null $message
     * @param  string|null $propertyPath
     * @return void
     * @throws \Assert\AssertionFailedException
     */
    public static function notNumericRange($value, $min, $max, $message = null, $propertyPath = null)
    {
        static::numeric($value);
        static::numeric($min);
        static::numeric($max);

        if ($value >= $min && $value <= $max) {
            $message = sprintf(
                $message ?: 'Value %d is between %d and %d',
                static::stringify($value),
                static::stringify($min),
                static::stringify($max)
            );

            throw static::createException($value, $message, static::INVALID_NUMERIC_RANGE, $propertyPath);
        }
    }

    /**
     * Assert that a string does not contain $needle
     *
     * @param  string      $value
     * @param  string      $needle
     * @param  string|null $message
     * @param  string|null $propertyPath
     * @return void
     * @throws \Assert\AssertionFailedException
     */
    public static function notContains($value, $needle, $message = null, $propertyPath = null)
    {
        static::string($value);
        static::string($needle);

        if (mb_strpos($value, $needle) !== false) {
            $message = sprintf(
                $message ?: 'Value "%s" contains "%s"',
                static::stringify($value),
                static::stringify($needle)
            );

            throw static::createException($value, $message, static::INVALID_STRING_CONTAINS, $propertyPath);
        }
    }
}
