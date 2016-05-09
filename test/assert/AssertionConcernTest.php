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
}
