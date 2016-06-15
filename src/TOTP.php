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

use function texdc\totp\assert\guard;

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
        guard($secret)->minLength(16, 'length of secret must be at least 16 characters')
            ->regex('/^[a-z2-7]+$/i', 'secret contains non-base32 characters');
        guard(strlen($secret))->isModulus(8, 'length of secret must be a multiple of 8');
        guard($digits)->numericRange(6, 8, 'digits must be 6, 7, or 8');
        guard($period)->integer();
        guard($offset)->nullOr()->integer();

        $seed  = $this->base32Decode($secret);
        $time  = $this->getTimestamp($period, $offset);
        $hash  = hash_hmac('sha1', $time, $seed, true);
        $bytes = substr($hash, ord(substr($hash, -1)) & 0x0F, 4);
        $value = unpack('N', $bytes);
        $otp   = ($value[1] & 0x7FFFFFFF) % pow(10, $digits);

        return sprintf("%'0{$digits}u", $otp);
    }

    /**
     * @param  int $length
     * @return string
     * @throws \Assert\AssertionFailedException
     */
    public function genSecret($length = 24)
    {
        guard($length)->min(16, 'length must be at least 16 characters')
            ->isModulus(8, 'length must be a multiple of 8');

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
        guard($account)->notBlank('account is required')
            ->notContains(':', 'account must not contain a colon character');
        guard($secret)->notBlank('secret is required');
        guard($digits)->nullOr()->digit();
        guard($period)->nullOr()->digit();
        guard($issuer)->notContains(':', 'issuer must not contain a colon character');

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
        $len = strlen($input);
        $num = $base = 0;

        for ($i = 0; $i < $len; $i++) {
            $num <<= 5;
            $num  += stripos(static::$base32Map, $input[$i]);
            $base  = ($base + 5) % 8;
            @$out .= $base < 5 ? chr(($num & (255 << $base)) >> $base) : null;
        }

        return $out;
    }

    /**
     * Generate a timestamp as a binary string
     *
     * @param  int $period
     * @param  int $offset
     * @return string
     */
    private function getTimestamp($period, $offset)
    {
        return "\0\0\0\0" . pack('N*', (int)floor(time() / $period) + ($offset * $period));
    }
}
