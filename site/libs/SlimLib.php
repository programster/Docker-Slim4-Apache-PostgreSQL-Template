<?php

/*
 * A library of functions to help with using Slim.
 */

class SlimLib
{
    public static function createJsonResponse(array $responseData) : \Slim\Psr7\Response
    {
        $responseBody = json_encode($responseData);
        $response = new \Slim\Psr7\Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/json');
        return $response;
    }
}
