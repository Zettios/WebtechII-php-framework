<?php

namespace Webtek\Core\Http;

use Psr\Http\Message\StreamInterface;

trait MessageTrait
{

    private array $AVAILABLE_PROTOCOL_VERSIONS = ["1.0", "1.1", "2.0"];

    private string $protocolVersion;
    private array $headers = [];
    private ?StreamInterface $body;

    public function setMessage(string $protocolVersion,
                                array $headers,
                                ?StreamInterface $body)
    {
        $this->setProtocolVersion($protocolVersion);

        $this->setHeaders($headers);

        $this->body = $body;
    }

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     * @return static
     */
    public function withProtocolVersion($version): self
    {
        if (!in_array($version, $this->AVAILABLE_PROTOCOL_VERSIONS)) {
            throw new \InvalidArgumentException("Protocol version is not supported.");
        }
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    /**
     * Sets protocol version, only available in private context (for construction)
     */
    private function setProtocolVersion(string $version): void
    {
        if (str_contains($version, "/")) {
            $version = explode("/", $version)[1];
        }

        if (!in_array($version, $this->AVAILABLE_PROTOCOL_VERSIONS)) {
            throw new \InvalidArgumentException("Protocol version is not supported.");
        }

        $this->protocolVersion = $version;
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders(): array
    {
        $preservedCaseHeaders = [];
        foreach ($this->headers as $header) {
            $preservedCaseHeaders[$header["preservedCaseName"]] = $header["value"];
        }

        return $preservedCaseHeaders;
    }

    /**
     * Sets headers for private context constructor.
     */
    public function setHeaders(array $headers): void
    {
        foreach ($headers as $key => $value) {
            $this->headers[strtolower($key)] = [
                "preservedCaseName" => $key,
                "value" => $value
            ];
        }
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name): bool
    {
        return array_key_exists(strtolower($name), $this->headers);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name): array
    {
        return array_key_exists(strtolower($name), $this->headers) ? $this->headers[strtolower($name)]["value"] : [];
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name): string
    {
        return array_key_exists(strtolower($name), $this->headers) ? implode(", ", $this->headers[strtolower($name)]["value"]) : "";
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value): self
    {
        $value = gettype($value) == "array" ? $value : [$value];
        $new = clone $this;
        $new->headers[strtolower($name)] = [
            "name" => $name,
            "value" => $value
        ];

        return $new;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value): self
    {
        $value = gettype($value) == "array" ? $value : [$value];
        $new = clone $this;
        if (array_key_exists(strtolower($name), $new->headers)) {
            $new->headers[strtolower($name)]["value"] = array_merge($new->headers[strtolower($name)]["value"], $value);
        } else {
            $new->headers[strtolower($name)] = [
                "name" => $name,
                "value" => $value
            ];
        }

        return $new;
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return static
     */
    public function withoutHeader($name): self
    {
        if (!array_key_exists(strtolower($name), $this->headers)) return $this;

        $new = clone $this;
        unset($new->headers[strtolower($name)]);

        return $new;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return static
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body): self
    {
        if (!($body instanceof StreamInterface)) {
            throw new \InvalidArgumentException("Provided body is not a StreamInterface.");
        }

        $new = clone $this;
        $new->body = $body;

        return $new;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return static
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBodyAsArray(array $body): self
    {
        if (!(is_array($body))) {
            throw new \InvalidArgumentException("Provided body is not an array.");
        }

        $new = clone $this;
        $new->body = $body;

        return $new;
    }
}