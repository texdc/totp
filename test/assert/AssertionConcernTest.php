<?php
/**
 * AssertionConcernTest.php
 *
 * @license   http://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @copyright 2016 George D. Cooksey, III
 */

namespace texdc\totp\test\assert;

use PHPUnit_Framework_TestCase as TestCase;
use function texdc\totp\assert\guard;

class AssertionConcernTest extends TestCase
{
    public function testAssertionConcernExtendsAssertionChain()
    {
        $this->assertInstanceOf('Assert\\AssertionChain', guard('test'));
    }

    public function testUsesProperAssertionClass()
    {
        guard(16)->isModulus(8)->numericRange(8, 32);
    }

    public function testUniqueInstances()
    {
        $this->assertTrue(guard('foo') !== guard(15));
    }

    public function testCallValidatesMethod()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage("Assertion 'foo' does not exist.");
        guard(5)->foo();
    }

    public function testAllUpdatesMethod()
    {
        guard([7,9])->all()->integer();
    }

    public function testNullOrIsValid()
    {
        guard(null)->nullOr()->alnum();
    }
}
