<?php

namespace Handscube\Kernel;

use Handscube\Kernel\Exceptions\InvalidException;

class ResponseBK extends \Handscube\Foundations\BaseResponse
{

    protected static $httpMessage = [
        // Information 1**
        self::HTTP_CONTINUE => 'Continue',
        self::HTTP_SWITCHING_PROTOCOLS => 'Switching Protocols',
        self::HTTP_PROCESSING => 'Processing',
        //Successful 2**
        self::HTTP_OK => 'OK',
        self::HTTP_CREATED => 'Created',
        self::HTTP_ACCEPTED => 'Accepted',
        self::HTTP_NONAUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
        self::HTTP_NO_CONTENT => 'No Content',
        self::HTTP_RESET_CONTENT => 'Reset Content',
        self::HTTP_PARTIAL_CONTENT => 'Partial Content',
        self::HTTP_MULTI_STATUS => 'Multi-Status',
        self::HTTP_ALREADY_REPORTED => 'Already Reported',
        self::HTTP_IM_USED => 'IM Used',
        // Redirection 3**
        self::HTTP_MULTIPLE_CHOICES => 'Multiple Choices',
        self::HTTP_MOVED_PERMANENTLY => 'Moved Permanently',
        self::HTTP_FOUND => 'Found',
        self::HTTP_SEE_OTHER => 'See Other',
        self::HTTP_NOT_MODIFIED => 'Not Modified',
        self::HTTP_USE_PROXY => 'Use Proxy',
        self::HTTP_UNUSED => '(Unused)',
        self::HTTP_TEMPORARY_REDIRECT => 'Temporary Redirect',
        self::HTTP_PERMANENT_REDIRECT => 'Permanent Redirect',
        // Client Error 4**
        self::HTTP_BAD_REQUEST => 'Bad Request',
        self::HTTP_UNAUTHORIZED => 'Unauthorized',
        self::HTTP_PAYMENT_REQUIRED => 'Payment Required',
        self::HTTP_FORBIDDEN => 'Forbidden',
        self::HTTP_NOT_FOUND => 'Not Found',
        self::HTTP_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::HTTP_NOT_ACCEPTABLE => 'Not Acceptable',
        self::HTTP_PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        self::HTTP_REQUEST_TIMEOUT => 'Request Timeout',
        self::HTTP_CONFLICT => 'Conflict',
        self::HTTP_GONE => 'Gone',
        self::HTTP_LENGTH_REQUIRED => 'Length Required',
        self::HTTP_PRECONDITION_FAILED => 'Precondition Failed',
        self::HTTP_REQUEST_ENTITY_TOO_LARGE => 'Request Entity Too Large',
        self::HTTP_REQUEST_URI_TOO_LONG => 'Request-URI Too Long',
        self::HTTP_UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
        self::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE => 'Requested Range Not Satisfiable',
        self::HTTP_EXPECTATION_FAILED => 'Expectation Failed',
        self::HTTP_IM_A_TEAPOT => 'I\'m a teapot',
        self::HTTP_MISDIRECTED_REQUEST => 'Misdirected Request',
        self::HTTP_UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
        self::HTTP_LOCKED => 'Locked',
        self::HTTP_FAILED_DEPENDENCY => 'Failed Dependency',
        self::HTTP_UPGRADE_REQUIRED => 'Upgrade Required',
        self::HTTP_PRECONDITION_REQUIRED => 'Precondition Required',
        self::HTTP_TOO_MANY_REQUESTS => 'Too Many Requests',
        self::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
        self::HTTP_CONNECTION_CLOSED_WITHOUT_RESPONSE => 'Connection Closed Without Response',
        self::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable For Legal Reasons',
        self::HTTP_CLIENT_CLOSED_REQUEST => 'Client Closed Request',
        // Server Error 5**
        self::HTTP_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::HTTP_NOT_IMPLEMENTED => 'Not Implemented',
        self::HTTP_BAD_GATEWAY => 'Bad Gateway',
        self::HTTP_SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::HTTP_GATEWAY_TIMEOUT => 'Gateway Timeout',
        self::HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
        self::HTTP_VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
        self::HTTP_INSUFFICIENT_STORAGE => 'Insufficient Storage',
        self::HTTP_LOOP_DETECTED => 'Loop Detected',
        self::HTTP_NOT_EXTENDED => 'Not Extended',
        self::HTTP_NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required',
        self::HTTP_NETWORK_CONNECTION_TIMEOUT_ERROR => 'Network Connect Timeout Error',
    ];

    /**
     * Get protocol.
     *
     * @return void
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Send a response with specified protocol.
     *
     * @param string $protocol
     * @return [Object]
     */
    public function sendWithProtocol(string $protocol)
    {
        if (isset(self::$validProtocols[$protocol])) {
            $clone = clone $this;
            $clone->protocol = (int) $protocol;
            return $clone;
        }
        throw new InvalidException("Protocol " . "$protocol" . "is invlid protocol.");
    }

}
