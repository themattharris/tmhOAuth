# tmhOAuth

An OAuth 1.0A library written in PHP by @themattharris, specifically for use
with the Twitter API.

**Disclaimer**: This project is a work in progress and may contain bugs.

## Goals

- Support OAuth 1.0A
- Use Authorisation headers instead of query string or POST parameters
- Allow uploading of images
- Provide enough information to assist with debugging

## Dependancies

The library has been tested with PHP 5.3+ and relies on CURL and hash_hmac. The
vast majority of hosting providers include these libraries and run with PHP 5.1+.

The code makes use of hash_hmac, which was introduced in PHP 5.1.2. If you version
of PHP is lower than this you should ask your hosting provider for an update.

## Usage

This will be built out later but for the moment review the examples for ways
the library can be used. Each example contains instructions on how to use it

## Change History
0.4   03 March 2011
      Fixed handling of parameters when using DELETE. Thanks to yusuke for reporting
      Fixed php_self to handle port numbers other than 80/443. Props: yusuke
      Updated function pr to use pre only when not running in CLI mode
      Add support for proxy servers. Props juanchorossi
      Function request now returns the HTTP status code. Props: kronenthaler
      Documentation fixes for xAuth. Props: 140dev
      Some minor code formatting changes

0.3   28 September 2010
      Moved entities rendering into the library

0.2   17 September 2010
      Added support for the Streaming API

0.14  17 September 2010
      Fixed authorisation header for use with OAuth Echo

0.13  17 September 2010
      Added use_ssl configuration parameter
      Fixed config array typo
      Removed v from the config
      Remove protocol from the host (configured by use_ssl)
      Added include for easier debugging

0.12  17 September 2010
      Moved curl options to config
      Added the ability for curl to follow redirects, default false

0.11  17 September 2010
      Fixed a bug in the GET requests

0.1   26 August 2010
      Initial beta version

## Community

License: Apache 2 (see included LICENSE file)

Follow me on Twitter: <http://twitter.com/themattharris>
Check out the Twitter Developer Resources: <http://dev.twitter.com>

## To Do

- Add good behavior logic to the Streaming API handler - i.e. on disconnect back off
- Add demo of responsible rate limit handling
- Async Curl support
- Split Utilities functions out