# Daytalytics WebAPI
***Interactive documentation***

## API Test Console

When using the interactive console provided by this documentation, you should use the client token & secret provided, or the following values to use the test token.

__Token__: *test12320090810* __Secret__: *6b53668dbf9e2b8548cf5a44aa19e8ab90a8bcc5*

To authorize, click the "Authorize" link in the upper right, and enter both the token and secret.

## API Service Requests

All API services, provide one or more resource endpoints that can be queried, the request format is as follows

*__{host}/{service}/{resource}?param=value...__*

*__{host}/{service}?param=value__* (for single resource services)

__All resources are HTTP:GET only.__

## Content Types

All resources are available in the following output mime type : formats

- *application/json* : json
- *application/json* : xml
- *application/vnd.php-object* : php 'serialized'
- *text/html* : text / human / debug

The response content type is controlled by the request header "__Accept__", this header must be defined for all requests, and the value must be one of the mime types listed above, failure to provide a valid accept header will result in an HTTP:400 response.

## Authorization/Authentication

Access to all API services/resources require authorization with an auth token, and authentication with HMAC request signing.

All clients will be provided with both an auth token and an auth secret". The token must be passed in the request header "__Api-Auth-Token__".

The secret must be used to generate a request signature (detailed below), which must be passed in the request header "__Api-Auth-Signature__".

Additionally all requests must parse the query param "__time__", which must contain the integer timestamp within the last 2 hours.

#### Request signature generation example (php)

```php
$authSecret = "auth-seceret-for-client";
$servicePath = 'google_search/ns';
$params = [
	'time' => time(),
	'keyword' => 'ipod'
];
$serviceUrl = $servicePath . '?' . http_build_query($params);
$signature = hash_hmac('sha1', $serviceUrl, $authSecret);
```
## Response Codes / Error Responses

All successful requests will return with the http status of 200, and the output containing the following indexes:

- *status* : 'OK'
- *status_message* : 'This was successful'
- *status_code* : 200
- *results* : result data if requested

All application error responses, will return with an http response code relevant to the error condition:

- *401* or *403* : authorization/authentication error
- *400* : invalid request
- *500* : internal error

Application errors will have the output containing the following indexes:

- *status* : 'ERROR'
- *status_message* : Relevant error message
- *status_code* : Relevant error code
- *error* : Error trace if the application is in debug mode

Any responses with an http response code in the error range, but no formatted output, is an internal error that the application could not resolve.
