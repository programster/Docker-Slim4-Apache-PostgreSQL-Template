<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class MiddlewareTrailingSlash implements \Psr\Http\Server\MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ($path != '/' && substr($path, -1) == '/')
        {
            // redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath(substr($path, 0, -1));

            if ($request->getMethod() == 'GET')
            {
                $response = new \Slim\Psr7\Response();
                $response = $response->withHeader('Location', (string)$uri)->withStatus(307);
            }
            else
            {
                $request = $request->withUri($uri);
                $response = $handler->handle($request);
            }
        }
        else
        {
            // do nothing. Just pass through.
            $response = $handler->handle($request);
        }

        return $response;
    }
}
