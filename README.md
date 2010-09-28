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
0.3   Moved entities rendering into the library
      28 September 2010

0.2   Added support for the Streaming API
      17 September 2010

0.14  Fixed authorisation header for use with OAuth Echo
      17 September 2010

0.13  Added use_ssl configuration parameter
      Fixed config array typo
      Removed v from the config
      Remove protocol from the host (configured by use_ssl)
      Added include for easier debugging
      17 September 2010

0.12  Moved curl options to config
      Added the ability for curl to follow redirects, default false
      17 September 2010

0.11  Fixed a bug in the GET requests
      17 September 2010

0.1   Initial beta version
      26 August 2010

## Community

License: Apache 2 (see included LICENSE file)

Follow me on Twitter: <http://twitter.com/themattharris>
Check out the Twitter Developer Resources: <http://dev.twitter.com>

## To Do

- Add good behavior logic to the Streaming API handler - i.e. on disconnect back off
- Add demo of responsible rate limit handling
- Async Curl support