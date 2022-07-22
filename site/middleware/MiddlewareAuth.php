<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class MiddlewareAuth implements \Psr\Http\Server\MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeContext = \Slim\Routing\RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();


        // return NotFound for non existent route
        if (empty($route))
        {
            throw new HttpNotFoundException($request);
        }

        $routeName = $route->getName();

        if
        (
               Auth::isLoggedIn() === false
            && in_array($routeName, Auth::getPublicRoutes()) === false
        )
        {
            $response = new \Slim\Psr7\Response(302);
            $response = $response->withHeader('Location', '/login');
        }
        else
        {
            $response = $handler->handle($request);
        }

        return $response;
    }
}
