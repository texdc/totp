<?php
/**
 * AssertionTest.php
 *
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright 2017 George D. Cooksey, III
 */

namespace texdc\totp\test\assert;

use PHPUnit_Framework_TestCase as TestCase;
use texdc\totp\assert\Assertion;

class AssertionTest extends TestCase
{
    public function testExtendsAssertion()
    {
        $assert = new Assertion();
        $this->assertInstanceOf('Assert\\Assertion', $assert);
    }

    public function testValidModulus()
    {
        Assertion::isModulus(24, 8);
        Assertion::isModulus(9, 3);
    }

    public function testInvalidModulus()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionCode(Assertion::INVALID_MODULUS);
        $this->expectExceptionMessage('Value 15 is not a modulus of 4');
        Assertion::isModulus(15, 4);
    }

    public function testValidNotModulus()
    {
        Assertion::notModulus(5, 3);
        Assertion::notModulus(45, 17);
    }

    public function testInvalidNotModulus()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionCode(Assertion::INVALID_MODULUS);
        $this->expectExceptionMessage('Value 12 is a modulus of 4');
        Assertion::notModulus(12, 4);
    }

    public function testValidNumericRange()
    {
        Assertion::numericRange(6, 6, 8);
        Assertion::numericRange(45, 40, 50);
    }

    public function testInvalidNumericRange()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionCode(Assertion::INVALID_NUMERIC_RANGE);
        $this->expectExceptionMessage('Value 12 is not between 15 and 20');
        Assertion::numericRange(12, 15, 20);
    }

    public function testValidNotNumericRange()
    {
        Assertion::notNumericRange(5, 6, 8);
        Assertion::notNumericRange(55, 40, 50);
    }

    public function testInvalidNotNumericRange()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionCode(Assertion::INVALID_NUMERIC_RANGE);
        $this->expectExceptionMessage('Value 18 is between 15 and 20');
        Assertion::notNumericRange(18, 15, 20);
    }

    public function testValidNotContains()
    {
        Assertion::notContains('foo', ':');
        Assertion::notContains('bar', ';');
    }

    public function testInvalidNotContains()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionCode(Assertion::INVALID_STRING_CONTAINS);
        $this->expectExceptionMessage('Value ":test" contains ":"');
        Assertion::notContains(':test', ':');
    }
}
