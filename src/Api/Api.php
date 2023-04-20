<?php

/**
 * @author Constan van Suchtelen van de Haere <constan@hostingbe.com>
 * @copyright 2023 HostingBE
 */

namespace hostingbe\phpapi;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

abstract class Api {

    protected $logger;
    protected $client;
    protected $useragent = 'Multiple options API HostingBE/1.0';
    protected $baseUri = 'https://api.hostingbe.com/api/v1/';
    const MAX_RETRIES = 3;


public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'handler' => $this->createHandlerStack(),
            'timeout'  => 20.0,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => $this->useragent,
            ],
         'verify' => true
        ]);
    }

protected function createHandlerStack() :object {
    $stack = HandlerStack::create();
    $stack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));
    return $this->createLoggingHandlerStack($stack);
    }  

protected function createLoggingHandlerStack(HandlerStack $stack) :object {
        $messageFormats = [
            '{method} {uri} HTTP/{version}',
            'HEADERS: {req_headers}',
            'BODY: {req_body}',
            'RESPONSE: {code} - {res_body}',
        ];
        foreach ($messageFormats as $messageFormat) {
            $stack->unshift(
                $this->createGuzzleLoggingMiddleware($messageFormat)
            );
        }
    return $stack;
    }


protected function createGuzzleLoggingMiddleware(string $messageFormat) {
        return Middleware::log(
            $this->logger,
            new MessageFormatter($messageFormat)
        );
}

protected function retryDecider() {
    return function (
        $retries,
        Request $request,
        Response $response = null,
        RequestException $exception = null
    ) {
    if ($retries >= self::MAX_RETRIES) {
    	return false;
    }
    /**
    *  retry on on timeouts
    */
    if ($exception instanceof ConnectException) {
        $this->logger->info('Timeout encountered, retrying');
        return true;
    }
            if ($response) {
                /**
                 *  retry on server error
                 */
                if ($response->getStatusCode() >= 500) {
                    $this->logger->info('Server 5xx error encountered, retrying...');
                    return true;
                }
            }
            return false;
        };
    }


/**
* delay 1s 2s 3s 4s 5s ...
*
* @return callable
*/
protected function retryDelay() {
        return function ($numberOfRetries) {
            return 1000 * $numberOfRetries;
        };
    }
}
?>