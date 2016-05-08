<?php
/**
 * TOTP.php
 *
 * @license   http://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @copyright 2016 George D. Cooksey, III
 * @copyright 2014 Robin Leffmann <djinn at stolendata dot net>
 *            https://github.com/stolendata/totp/
 */

namespace texdc\totp;

use texdc\totp\assert\Assertion;

/**
 * a simple TOTP (RFC 6238) class using the SHA1 default
 *
 * @author George D. Cooksey, III
 * @author Robin Leffmann
 */
class TOTP
{
    /**
     * @var string
     */
    private static $base32Map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * @param  string $secret
     * @param  int    $digits
     * @param  int    $period
     * @param  int    $offset
     * @return string
     * @throws \Assert\AssertionFailedException
     */
    public function getOTP($secret, $digits = 6, $period = 30, $offset = null)
    {
        Assertion::minLength($secret, 16, 'length of secret must be at least 16 characters');
        Assertion::isModulus(strlen($secret), 8, 'length of secret must be a multiple of 8');
        Assertion::regex($secret, '/^[a-z2-7]+$/i', 'secret contains non-base32 characters');
        Assertion::numericRange($digits, 6, 8, 'digits must be 6, 7, or 8');
        Assertion::digit($period);
        Assertion::nullOrDigit($offset);

        $seed = static::base32Decode($secret);
        $time = str_pad(pack('N', intval(time() / $period) + $offset), 8, "\x00", STR_PAD_LEFT);
        $hash = hash_hmac('sha1', $time, $seed, false);
        $otp  = (hexdec(substr($hash, hexdec($hash[39]) * 2, 8)) & 0x7fffffff) % pow(10, $digits);

        return sprintf("%'0{$digits}u", $otp);
    }

    /**
     * @param  int $length
     * @return string
     * @throws \Assert\AssertionFailedException
     */
    public function genSecret($length = 24)
    {
        Assertion::min($length, 16, 'length must be at least 16 characters');
        Assertion::isModulus($length, 8, 'length must be a multiple of 8');

        while ($length--) {
            $c = @gettimeofday()['usec'] % 53;
            while ($c--) {
                mt_rand();
            }
            @$secret .= static::$base32Map[mt_rand(0, 31)];
        }

        return $secret;
    }

    /**
     * @param  string $account
     * @param  string $secret
     * @param  int    $digits
     * @param  int    $period
     * @param  string $issuer
     * @return string
     */
    public function genURI($account, $secret, $digits = null, $period = null, $issuer = '')
    {
        Assertion::notBlank($account, 'account is required');
        Assertion::notContains($account, ':', 'account must not contain a colon character');
        Assertion::notBlank($secret, 'secret is required');
        Assertion::nullOrDigit($digits);
        Assertion::nullOrDigit($period);
        Assertion::notContains($issuer, ':', 'issuer must not contain a colon character');

        $account = rawurlencode($account);
        $issuer  = rawurlencode($issuer);
        $label   = empty($issuer) ? $account : "$issuer:$account";

        return 'otpauth://totp/' . $label . "?secret=$secret" .
                 (is_null($digits) ? '' : "&digits=$digits") .
                 (is_null($period) ? '' : "&period=$period") .
                 (empty($issuer)   ? '' : "&issuer=$issuer");
    }

    /**
     * @param  string $input
     * @return string
     */
    private function base32Decode($input)
    {
        $l = strlen($input);
        $n = $bs = 0;

        for ($i = 0; $i < $l; $i++) {
            $n <<= 5;
            $n += stripos(static::$base32Map, $input[$i]);
            $bs = ($bs + 5) % 8;
            @$out .= $bs < 5 ? chr(($n & (255 << $bs)) >> $bs) : null;
        }

        return $out;
    }
}
