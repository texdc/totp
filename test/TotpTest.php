<?php
/**
 * TotpTest.php
 *
 * @license   http://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @copyright 2016 George D. Cooksey, III
 */

namespace texdc\totp\test;

use PHPUnit_Framework_TestCase as TestCase;
use texdc\totp\TOTP;

class TotpTest extends TestCase
{
    public function testGetOtpChecksSecretLength()
    {
        $result = TOTP::getOTP('foo');
        $this->assertArrayHasKey('err', $result);
    }

    public function testGetOtpChecksSecretCharacters()
    {
        $result = TOTP::getOTP(str_repeat('^', 16));
        $this->assertArrayHasKey('err', $result);
    }

    public function testGetOtpChecksDigits()
    {
        $result = TOTP::getOTP(str_repeat('a5', 16), 1);
        $this->assertArrayHasKey('err', $result);
    }

    public function testGetOtpGeneratesOtp()
    {
        $result = TOTP::getOTP(str_repeat('a5', 16));
        $this->assertArrayHasKey('otp', $result);
        $this->assertEquals(6, strlen($result['otp']));
    }

    public function testGenSecretChecksLength()
    {
        $result = TOTP::genSecret(5);
        $this->assertArrayHasKey('err', $result);
    }

    public function testGenSecretGeneratesSecret()
    {
        $result = TOTP::genSecret();
        $this->assertArrayHasKey('secret', $result);
        $this->assertEquals(24, strlen($result['secret']));
    }

    public function testGenUriChecksEmptyAccount()
    {
        $result = TOTP::genURI('', 'foo');
        $this->assertArrayHasKey('err', $result);
    }

    public function testGenUriChecksEmptySecret()
    {
        $result = TOTP::genURI('foo', '');
        $this->assertArrayHasKey('err', $result);
    }

    public function testGenUriChecksAccontForColon()
    {
        $result = TOTP::genURI('foo:', 'bar');
        $this->assertArrayHasKey('err', $result);
    }

    public function testGenUriChecksIssuerForColon()
    {
        $result = TOTP::genURI('foo', 'bar', null, null, ':');
        $this->assertArrayHasKey('err', $result);
    }

    public function testGenUriGeneratesUri()
    {
        $result = TOTP::genURI('foo', 'bar');
        $this->assertArrayHasKey('uri', $result);
        $this->assertContains('otpauth://totp/', $result['uri']);
    }
}
