# tmhOAuth

An OAuth 1.0A library written in PHP by @themattharris, specifically for use
with the Twitter API.

**Important**: If you used custom HTTP request headers they should now be defined
as `'key' => 'value'` pairs instead of complete `'key: value'` strings.

If you previously used version 0.4 be aware the utility functions
have now been broken into their own file. Before you use version 0.5+ in your app
test locally to ensure your code doesn't need tmhUtilities included.

**Disclaimer**: This project is a work in progress. Please use the issue tracker
to report any enhancements or issues you encounter.

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

### 0.56 - 29 September 2011
- Fixed version reference in the UserAgent
- Updated tmhUtilities::entify with support for media
- Updated tmhUtilities::entify with support for multibyte characters. Props: andersonshatch

### 0.55 - 29 September 2011
- Added support for content encoding. Defaults to whatever localhost supports. Props: yusuke

### 0.54 - 29 September 2011
- User-Agent is now configurable and includes the current version number of the script
- Updated the Streaming examples to use SSL

### 0.53 - 15 July 2011
- Fixed issue where headers were being duplicated if the library was called more than once.
- Updated examples to fit the new location of access tokens and secrets on dev.twitter.com
- Added Photo Tweet example

### 0.52 - 06 July 2011
- Fixed issue where the preference for include_time in create_nonce was being ignored

### 0.51 - 06 July 2011
- Use isset instead of suppress errors. Props: funkatron
- Added example of using the Search API
- Added example of using friends/ids and users/lookup to get details of a users friends
- Added example of the authorize OAuth webflow

### 0.5 - 29 March 2011
- Moved utility functions out of the main class and into the tmhUtilities class.
- Added the ability to send OAuth parameters as part of the querystring or POST body.
- Section 3.4.1.2 says the url must be lowercase so prepare URL now does this.
- Added a convenience method for accessing the safe_encode/decode transforms.
- Updated the examples to use the new utilities library.
- Added examples for sitestreams and userstreams.
- Added a more advanced streaming API example.

### 0.4 - 03 March 2011
- Fixed handling of parameters when using DELETE. Thanks to yusuke for reporting
- Fixed php_self to handle port numbers other than 80/443. Props: yusuke
- Updated function pr to use pre only when not running in CLI mode
- Add support for proxy servers. Props juanchorossi
- Function request now returns the HTTP status code. Props: kronenthaler
- Documentation fixes for xAuth. Props: 140dev
- Some minor code formatting changes

### 0.3 - 28 September 2010
- Moved entities rendering into the library

### 0.2 - 17 September 2010
- Added support for the Streaming API

### 0.14 - 17 September 2010
- Fixed authorisation header for use with OAuth Echo

### 0.13 - 17 September 2010
- Added use_ssl configuration parameter
- Fixed config array typo
- Removed v from the config
- Remove protocol from the host (configured by use_ssl)
- Added include for easier debugging

### 0.12 - 17 September 2010

- Moved curl options to config
- Added the ability for curl to follow redirects, default false

### 0.11 - 17 September 2010

- Fixed a bug in the GET requests

### 0.1 - 26 August 2010

- Initial beta version

## Community

License: Apache 2 (see included LICENSE file)

Follow me on Twitter: <https://twitter.com/intent/follow?screen_name=themattharris>
Check out the Twitter Developer Resources: <http://dev.twitter.com>

## To Do

- Add good behavior logic to the Streaming API handler - i.e. on disconnect back off
- Async Curl support