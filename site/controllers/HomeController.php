<?php


class HomeController extends AbstractSlimController
{
    public static function registerRoutes($app)
    {
        $app->get('/', function (Slim\Psr7\Request $request, Slim\Psr7\Response $response, $args) {
            $body = $response->getBody();
            $body->write((string)new ViewHomePage()); // returns number of bytes written
            $newResponse = $response->withBody($body);
            return $newResponse;
        });
    }
}
