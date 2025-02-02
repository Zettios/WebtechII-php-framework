<?php

namespace Webtek\Core\Http;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
    use MessageTrait;

    private const AVAILABLE_METHODS = ["GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS", "TRACE", "CONNECT", "HEAD"];

    private string $method;
    private Uri $uri;
    private mixed $requestTarget;

    public function __construct(string $method,
                                UriInterface|string $uri,
                                string $protocolVersion,
                                array $headers = [],
                                StreamInterface $body = null,
                                mixed $requestTarget = null)
    {
        $this->setMethod($method);
        $this->setUri($uri);
        $this->setMessage($protocolVersion, $headers, $body);
        $this->setRequestTarget($requestTarget);
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method): self
    {
        if (!array_key_exists($method, self::AVAILABLE_METHODS)) {
            throw new \InvalidArgumentException("Provided method is a invalid HTTP method.");
        }

        $new = clone $this;
        $new->method = $method;

        return $new;
    }

    private function setMethod(string $method): void
    {
        if (!in_array($method, self::AVAILABLE_METHODS)) {
            throw new InvalidArgumentException("No method named " . $method);
        }
        $this->method = $method;
    }

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget(): string
    {
        if (isset($this->requestTarget)) return $this->requestTarget;
        if (isset($this->uri)) return $this->uri->getPath() . ($this->uri->getQuery() ?? "");

        return "/";
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return static
     */
    public function withRequestTarget($requestTarget): self
    {
        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $this;
    }

    private function setRequestTarget(mixed $requestTarget): void
    {
        $this->requestTarget = $requestTarget;
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        $new = clone $this;
//        if ($preserveHost) {
//            if ($this->uri->getHost() !== null){
//                if (!$uri->getHost() === null) {
//
//                }
//            }
//        }
        $new->uri = $uri;
        return $new;
    }

    public function setUri(string|UriInterface $uri): void
    {
        if (gettype($uri) == "string") {
            $uri = Uri::fromString($uri);
        }
        $this->uri = $uri;
    }
}