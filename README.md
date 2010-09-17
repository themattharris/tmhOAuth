# tmhOAuth

An OAuth 1.0A library written in PHP by @themattharris, specifically for use
with the Twitter API.

**Disclaimer**: This project is a work in progress and may contain bugs.

## Goals

- Support OAuth 1.0A
- Use Authorisation headers instead of query string or POST parameters
- Allow uploading of images
- Provide enough information to assist with debugging

## Using

This will be built out later but for the moment review the examples for ways
the library can be used. Each example contains instructions on how to use it

## Change History
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
