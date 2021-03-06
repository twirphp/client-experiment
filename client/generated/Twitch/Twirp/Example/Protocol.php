<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!

namespace Twitch\Twirp\Example;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\MessageFactory;
use Http\Message\StreamFactory;
use Psr\Http\Message\ResponseInterface;
use Twirp\ErrorCode;
use Twirp\TwirpError;

/**
 * Protocol implements some common, protocol specific logic (like error responses, etc).
 */
trait Protocol
{
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * Used when the twirp server cannot route a request.
     *
     * @param string $msg
     * @param string $method
     * @param string $url
     *
     * @return TwirpError
     */
    private function badRoute($msg, $method, $url)
    {
        $e = TwirpError::newError(ErrorCode::BadRoute, $msg);
        $e = $e->withMeta('twirp_invalid_route', $method . ' ' . $url);

        return $e;
    }

    /**
     * Writes Twirp errors in the response and triggers hooks.
     *
     * @param array        $ctx
     * @param \Twirp\Error $e
     *
     * @return ResponseInterface
     */
    private function writeError(array $ctx, \Twirp\Error $e)
    {
        $statusCode = ErrorCode::serverHTTPStatusFromErrorCode($e->code());

        $body = $this->getStreamFactory()->createStream(json_encode([
            'code' => $e->code(),
            'msg' => $e->msg(),
            'meta' => $e->metaMap(),
        ]));

        return $this->getMessageFactory()
            ->createResponse($statusCode)
            ->withHeader('Content-Type', 'application/json') // Error responses are always JSON (instead of protobuf)
            ->withBody($body);
    }

    /**
     * Returns a message factory instance.
     *
     * @return MessageFactory
     */
    private function getMessageFactory()
    {
        if ($this->messageFactory === null) {
            $this->messageFactory = MessageFactoryDiscovery::find();
        }

        return $this->messageFactory;
    }

    /**
     * Returns a stream factory instance.
     *
     * @return StreamFactory
     */
    private function getStreamFactory()
    {
        if ($this->streamFactory === null) {
            $this->streamFactory = StreamFactoryDiscovery::find();
        }

        return $this->streamFactory;
    }
}
