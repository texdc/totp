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
    private $totp;

    protected function setUp()
    {
        $this->totp = new TOTP();
    }

    public function testGetOtpChecksSecretLength()
    {
        $result = $this->totp->getOTP('foo');
        $this->assertArrayHasKey('err', $result);
    }

    public function testGetOtpChecksSecretCharacters()
    {
        $result = $this->totp->getOTP(str_repeat('^', 16));
        $this->assertArrayHasKey('err', $result);
    }

    public function testGetOtpChecksDigits()
    {
        $result = $this->totp->getOTP(str_repeat('a5', 16), 1);
        $this->assertArrayHasKey('err', $result);
    }

    public function testGetOtpGeneratesOtp()
    {
        $result = $this->totp->getOTP(str_repeat('a5', 16));
        $this->assertArrayHasKey('otp', $result);
        $this->assertEquals(6, strlen($result['otp']));
    }

    public function testGenSecretChecksLength()
    {
        $result = $this->totp->genSecret(5);
        $this->assertArrayHasKey('err', $result);
    }

    public function testGenSecretGeneratesSecret()
    {
        $result = $this->totp->genSecret();
        $this->assertArrayHasKey('secret', $result);
        $this->assertEquals(24, strlen($result['secret']));
    }

    public function testGenUriChecksEmptyAccount()
    {
        $result = $this->totp->genURI('', 'foo');
        $this->assertArrayHasKey('err', $result);
    }

    public function testGenUriChecksEmptySecret()
    {
        $result = $this->totp->genURI('foo', '');
        $this->assertArrayHasKey('err', $result);
    }

    public function testGenUriChecksAccontForColon()
    {
        $result = $this->totp->genURI('foo:', 'bar');
        $this->assertArrayHasKey('err', $result);
    }

    public function testGenUriChecksIssuerForColon()
    {
        $result = $this->totp->genURI('foo', 'bar', null, null, ':');
        $this->assertArrayHasKey('err', $result);
    }

    public function testGenUriGeneratesUri()
    {
        $result = $this->totp->genURI('foo', 'bar');
        $this->assertArrayHasKey('uri', $result);
        $this->assertContains('otpauth://totp/', $result['uri']);
    }
}
