<?php namespace Tempest\HTTP;

/**
 * Manages HTTP status codes.
 * Source: http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 */
class Status
{

	/**
	 * Standard response for successful HTTP requests. The actual response will depend on the request method used. In a GET request, the response will contain an entity corresponding to the requested resource. In a POST request the response will contain an entity describing or containing the result of the action.
	 */
	const OK = 200;

	/**
	 * The request has been fulfilled and resulted in a new resource being created.
	 */
	const CREATED = 201;

	/**
	 * The request has been accepted for processing, but the processing has not been completed. The request might or might not eventually be acted upon, as it might be disallowed when processing actually takes place.
	 */
	const ACCEPTED = 202;

	/**
	 * The server successfully processed the request, but is returning information that may be from another source.
	 */
	const NON_AUTHORITATIVE_INFORMATION = 203;

	/**
	 * The server successfully processed the request, but is not returning any content. Usually used as a response to a successful delete request.
	 */
	const NO_CONTENT = 204;

	/**
	 * The server successfully processed the request, but is not returning any content. Unlike a 204 response, this response requires that the requester reset the document view.
	 */
	const RESET_CONTENT = 205;

	/**
	 * The server is delivering only part of the resource due to a range header sent by the client. The range header is used by tools like wget to enable resuming of interrupted downloads, or split a download into multiple simultaneous streams.
	 */
	const PARTIAL_CONTENT = 206;

	/**
	 * Indicates multiple options for the resource that the client may follow. It, for instance, could be used to present different format options for video, list files with different extensions, or word sense disambiguation.
	 */
	const MULTIPLE_CHOICES = 300;

	/**
	 * This and all future requests should be directed to the given URI.
	 */
	const MOVED_PERMANENTLY = 301;

	/**
	 * This is an example of industry practice contradicting the standard. The HTTP/1.0 specification (RFC 1945) required the client to perform a temporary redirect (the original describing phrase was "Moved Temporarily"), but popular browsers implemented 302 with the functionality of a 303 See Other. Therefore, HTTP/1.1 added status codes 303 and 307 to distinguish between the two behaviours. However, some Web applications and frameworks use the 302 status code as if it were the 303.
	 */
	const FOUND = 302;

	/**
	 * The response to the request can be found under another URI using a GET method. When received in response to a POST (or PUT/DELETE), it should be assumed that the server has received the data and the redirect should be issued with a separate GET message.
	 */
	const SEE_OTHER = 303;

	/**
	 * Indicates that the resource has not been modified since the version specified by the request headers If-Modified-Since or If-Match. This means that there is no need to retransmit the resource, since the client still has a previously-downloaded copy.
	 */
	const NOT_MODIFIED = 304;

	/**
	 * The requested resource is only available through a proxy, whose address is provided in the response. Many HTTP clients (such as Mozilla and Internet Explorer) do not correctly handle responses with this status code, primarily for security reasons.
	 */
	const USE_PROXY = 305;

	/**
	 * No longer used. Originally meant "Subsequent requests should use the specified proxy."
	 */
	const SWITCH_PROXY = 306;

	/**
	 * In this case, the request should be repeated with another URI; however, future requests should still use the original URI. In contrast to how 302 was historically implemented, the request method is not allowed to be changed when reissuing the original request. For instance, a POST request should be repeated using another POST request.
	 */
	const TEMPORARY_REDIRECT = 307;

	/**
	 * The request, and all future requests should be repeated using another URI. 307 and 308 (as proposed) parallel the behaviours of 302 and 301, but do not allow the HTTP method to change. So, for example, submitting a form to a permanently redirected resource may continue smoothly.
	 */
	const PERMANENT_REDIRECT = 308;

	/**
	 * The request cannot be fulfilled due to bad syntax.
	 */
	const BAD_REQUEST = 400;

	/**
	 * Similar to 403 Forbidden, but specifically for use when authentication is required and has failed or has not yet been provided. The response must include a WWW-Authenticate header field containing a challenge applicable to the requested resource. See Basic access authentication and Digest access authentication.
	 */
	const UNAUTHORIZED = 401;

	/**
	 * The request was a valid request, but the server is refusing to respond to it. Unlike a 401 Unauthorized response, authenticating will make no difference.
	 */
	const FORBIDDEN = 403;

	/**
	 * The requested resource could not be found but may be available again in the future. Subsequent requests by the client are permissible.
	 */
	const NOT_FOUND = 404;

	/**
	 * A request was made of a resource using a request method not supported by that resource; for example, using GET on a form which requires data to be presented via POST, or using PUT on a read-only resource.
	 */
	const METHOD_NOT_ALLOWED = 405;

	/**
	 * The requested resource is only capable of generating content not acceptable according to the Accept headers sent in the request.
	 */
	const NOT_ACCEPTABLE = 406;

	/**
	 * The client must first authenticate itself with the proxy.
	 */
	const PROXY_AUTHENTICATION_REQUIRED = 407;

	/**
	 * The server timed out waiting for the request. According to HTTP specifications: "The client did not produce a request within the time that the server was prepared to wait. The client MAY repeat the request without modifications at any later time."
	 */
	const REQUEST_TIMEOUT = 408;

	/**
	 * Indicates that the request could not be processed because of conflict in the request, such as an edit conflict in the case of multiple updates.
	 */
	const CONFLICT = 409;

	/**
	 * Indicates that the resource requested is no longer available and will not be available again. This should be used when a resource has been intentionally removed and the resource should be purged. Upon receiving a 410 status code, the client should not request the resource again in the future. Clients such as search engines should remove the resource from their indices. [citation needed] Most use cases do not require clients and search engines to purge the resource, and a "404 Not Found" may be used instead.
	 */
	const GONE = 410;

	/**
	 * The request did not specify the length of its content, which is required by the requested resource.
	 */
	const LENGTH_REQUIRED = 411;

	/**
	 * The server does not meet one of the preconditions that the requester put on the request.
	 */
	const PRECONDITION_FAILED = 412;

	/**
	 * The request is larger than the server is willing or able to process.
	 */
	const REQUEST_ENTITY_TOO_LARGE = 413;

	/**
	 * The URI provided was too long for the server to process. Often the result of too much data being encoded as a query-string of a GET request, in which case it should be converted to a POST request.
	 */
	const REQUEST_RI_TOO_LONG = 414;

	/**
	 * The request entity has a media type which the server or resource does not support. For example, the client uploads an image as image/svg+xml, but the server requires that images use a different format.
	 */
	const UNSUPPORTED_MEDIA_TYPE = 415;

	/**
	 * The client has asked for a portion of the file, but the server cannot supply that portion. For example, if the client asked for a part of the file that lies beyond the end of the file.
	 */
	const REQUESTED_RANGE_NOT_SATISFIABLE = 416;

	/**
	 * The server cannot meet the requirements of the Expect request-header field.
	 */
	const EXPECTATION_FAILED = 417;

	/**
	 * The client should switch to a different protocol such as TLS/1.0.
	 */
	const UPGRADE_REQUIRED = 426;

	/**
	 * The origin server requires the request to be conditional. Intended to prevent "the 'lost update' problem, where a client GETs a resource's state, modifies it, and PUTs it back to the server, when meanwhile a third party has modified the state on the server, leading to a conflict."
	 */
	const PRECONDITION_REQUIRED = 428;

	/**
	 * The user has sent too many requests in a given amount of time. Intended for use with rate limiting schemes.
	 */
	const TOO_MANY_REQUESTS = 429;

	/**
	 * The server is unwilling to process the request because either an individual header field, or all the header fields collectively, are too large.
	 */
	const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

	/**
	 * A generic error message, given when an unexpected condition was encountered and no more specific message is suitable.
	 */
	const INTERNAL_SERVER_ERROR = 500;

	/**
	 * The server either does not recognize the request method, or it lacks the ability to fulfil the request. Usually this implies future availability (e.g., a new feature of a web-service API).
	 */
	const NOT_IMPLEMENTED = 501;

	/**
	 * The server was acting as a gateway or proxy and received an invalid response from the upstream server.
	 */
	const BAD_GATEWAY = 502;

	/**
	 * The server is currently unavailable (because it is overloaded or down for maintenance). Generally, this is a temporary state.
	 */
	const SERVICE_UNAVAILABLE = 503;

	/**
	 * The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.
	 */
	const GATEWAY_TIMEOUT = 504;

	/**
	 * The server does not support the HTTP protocol version used in the request.
	 */
	const HTTP_VERSION_NOT_SUPPORTED = 505;

	/**
	 * Transparent content negotiation for the request results in a circular reference.
	 */
	const VARIANT_ALSO_NEGOTIATES = 506;

	/**
	 * Further extensions to the request are required for the server to fulfil it.
	 */
	const NOT_EXTENDED = 510;

	/**
	 * The client needs to authenticate to gain network access. Intended for use by intercepting proxies used to control access to the network (e.g., "captive portals" used to require agreement to Terms of Service before granting full Internet access via a Wi-Fi hotspot).
	 */
	const NETWORK_AUTHENTICATION_REQUIRED = 511;

}