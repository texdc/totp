<?php
/**
 * TotpTest.php
 *
 * @license   http://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @copyright 2017 George D. Cooksey, III
 */

namespace texdc\totp\test;

use PHPUnit_Framework_TestCase as TestCase;
use texdc\totp\TOTP;

class TotpTest extends TestCase
{
    private $totp;

    protected function setUp()
    {
        $this->totp = new TOTP();
    }

    public function testGetOtpChecksSecretLength()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionMessage('length of secret must be at least 16 characters');
        $result = $this->totp->getOTP('foo');
    }

    public function testGetOtpChecksSecretModulus()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionMessage('length of secret must be a multiple of 8');
        $result = $this->totp->getOTP(str_repeat('foo', 6));
    }

    public function testGetOtpChecksSecretCharacters()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionMessage('secret contains non-base32 characters');
        $result = $this->totp->getOTP(str_repeat('^', 16));
    }

    public function testGetOtpChecksDigits()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionMessage('digits must be 6, 7, or 8');
        $result = $this->totp->getOTP(str_repeat('a5', 16), 1);
    }

    public function testGetOtpGeneratesOtp()
    {
        $result = $this->totp->getOTP(str_repeat('a5', 16));
        $this->assertInternalType('string', $result);
        $this->assertEquals(6, strlen($result));
    }

    public function testGenSecretChecksLength()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionMessage('length must be at least 16 characters');
        $result = $this->totp->genSecret(5);
    }

    public function testGenSecretChecksModulus()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionMessage('length must be a multiple of 8');
        $result = $this->totp->genSecret(19);
    }

    public function testGenSecretGeneratesSecret()
    {
        $result = $this->totp->genSecret();
        $this->assertInternalType('string', $result);
        $this->assertEquals(24, strlen($result));
    }

    public function testGenUriChecksEmptyAccount()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionMessage('account is required');
        $result = $this->totp->genURI('', 'foo');
    }

    public function testGenUriChecksEmptySecret()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionMessage('secret is required');
        $result = $this->totp->genURI('foo', '');
    }

    public function testGenUriChecksAccontForColon()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionMessage('account must not contain a colon character');
        $result = $this->totp->genURI('foo:', 'bar');
    }

    public function testGenUriChecksIssuerForColon()
    {
        $this->expectException('Assert\AssertionFailedException');
        $this->expectExceptionMessage('issuer must not contain a colon character');
        $result = $this->totp->genURI('foo', 'bar', null, null, ':test');
    }

    public function testGenUriGeneratesUri()
    {
        $result = $this->totp->genURI('foo', 'bar');
        $this->assertInternalType('string', $result);
        $this->assertContains('otpauth://totp/', $result);
    }
}
