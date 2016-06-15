TOTP (RFC 6238)
===============

TOTP is a simple, compact and bare-bones PHP class for calculating
[TOTP](https://en.wikipedia.org/wiki/Time-based_One-time_Password_Algorithm) tokens
using the SHA1 default, commonly used for two-factor authentication in mobile apps
such as Google Authenticator. It comprises three public methods of which only one
is necessary to call to get a token.


Usage
-----

Simply call `$totp->getOTP( $secret [, $digits = 6 [, $period = 30 [, $offset = null ]]] )`
which returns a string holding the authentication token.

The other two functions are meant to be convenient utilities:

`$totp->genSecret( [ $length = 24 ] )` generates a TOTP-compatible pseudorandom secret
in Base32 ASCII, returning a string holding the random secret.

`$totp->genURI( $account, $secret [, $digits = null [, $period = null [, $issuer = '' ]]] )`
returns a string holding an `otpauth://` style URI providing the supplied parameters,
which can be embedded in a QR code image.

> NOTE: All arguments are validated and every method may throw an
`Assert\AssertionFailedException` detailing the error.


License
-------

TOTP is released under the [Creative Commons BY-NC-SA 4.0 license](http://creativecommons.org/licenses/by-nc-sa/4.0/).

Portions Copyright (c) 2014 [Robin Leffman](https://github.com/stolendata).
The original source is [available on github](https://github.com/stolendata/totp).
