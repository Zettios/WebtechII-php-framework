<?php

namespace Webtek\Core;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class HttpFactory implements
    RequestFactoryInterface,
    ServerRequestFactoryInterface,
    ResponseFactoryInterface,
    StreamFactoryInterface 
{

    /**
     * Schrijf dit op voor morgen:
     * Het idee dat ik hier nu bij heb is dat er minimaal een Request en Response object uit moet komen.
     * Handig om eerst met die twee dingen te beginnen zodat het niet te overwhelmend is.
     * Stap 1: Check of er een request methode is, zo ja maak request. Check of er een response code is, zo ja maak response
     * Stap 2: Met een functie moeten deze objecten (of object) worden aangemaakt.
     * Stap 2.1: Met deze functie word met de hand van de superglobals een Uri object gemaakt en doorgegeven aan de createRequest methode
     * Stap 2.2: Met deze functie word aan de hand van php functies een response object gemaakt met createResponse
     * Stap 3: Controleren of de huidige implementatie van onze Request en Response object goed is (volgens mij moet er nog wat aanpassingen komen)
     * Stap 4: Wanneer de objecten zijn gemaakt, geef ze terug. De functie die ze aanmaakt moet dus één of beide terug geven.
     *
     * Neem nu rust omdat moe.
     */

    const REQUEST = 1;
    const RESPONSE = 2;

    public function makeObjects(int $selector): ?MessageInterface
    {
        switch($selector) {
            case 1:
                $requestMethod = $_SERVER['REQUEST_METHOD'];
                $uri = $_SERVER['REQUEST_URI'];
                $request = $this->createServerRequest($requestMethod, $uri);
                return $request;
             case 2:
                 return $this->createResponse();
            default:
                return null;
        }

    }

    /**
     * Create a new server request.
     *
     * Note that server-params are taken precisely as given - no parsing/processing
     * of the given values is performed, and, in particular, no attempt is made to
     * determine the HTTP method or URI, which must be provided explicitly.
     *
     * @param string $method The HTTP method associated with the request.
     * @param UriInterface|string $uri The URI associated with the request. If
     *     the value is a string, the factory MUST create a UriInterface
     *     instance based on it.
     * @param array $serverParams Array of SAPI parameters with which to seed
     *     the generated request instance.
     *
     * @return ServerRequestInterface
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new ServerRequest($method, $uri, $serverParams);
    }

    /**
     * Create a new request.
     *
     * @param string $method The HTTP method associated with the request.
     * @param UriInterface|string $uri The URI associated with the request. If
     *     the value is a string, the factory MUST create a UriInterface
     *     instance based on it.
     *
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        $server = $_SERVER;
        $headers = [];
        foreach ($_SERVER as $parm => $value){
            $headers[$parm] = $value;
        }

        return new Request( $server['REQUEST_METHOD'],
                            $server['REQUEST_URI'],
                            $server['SERVER_PROTOCOL'],
                            $headers,
                            $uri);
    }


    /**
     * Create a new response.
     *
     * @param int $code HTTP status code; defaults to 200
     * @param string $reasonPhrase Reason phrase to associate with status code
     *     in generated response; if none is provided implementations MAY use
     *     the defaults as suggested in the HTTP specification.
     *
     * @return ResponseInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response('1.1', [], null, $code, $reasonPhrase);
    }

    /**
     * Create a new stream from a string.
     *
     * The stream SHOULD be created with a temporary resource.
     *
     * @param string $content String content with which to populate the stream.
     *
     * @return StreamInterface
     */
    public function createStream(string $content = ''): StreamInterface
    {
        // TODO: Implement createStream() method.
    }

    /**
     * Create a stream from an existing file.
     *
     * The file MUST be opened using the given mode, which may be any mode
     * supported by the `fopen` function.
     *
     * The `$filename` MAY be any string supported by `fopen()`.
     *
     * @param string $filename Filename or stream URI to use as basis of stream.
     * @param string $mode Mode with which to open the underlying filename/stream.
     *
     * @return StreamInterface
     * @throws \RuntimeException If the file cannot be opened.
     * @throws \InvalidArgumentException If the mode is invalid.
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        // TODO: Implement createStreamFromFile() method.
    }

    /**
     * Create a new stream from an existing resource.
     *
     * The stream MUST be readable and may be writable.
     *
     * @param resource $resource PHP resource to use as basis of stream.
     *
     * @return StreamInterface
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        // TODO: Implement createStreamFromResource() method.
    }
}