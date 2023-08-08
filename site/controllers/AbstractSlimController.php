<?php


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractSlimController
{
    protected Slim\Psr7\Request $m_request;
    protected Slim\Psr7\Response $m_response;
    protected $m_args;


    public function __construct(RequestInterface $request, ResponseInterface $response, $args)
    {
        $this->m_request = $request;
        $this->m_response = $response;
        $this->m_args = $args;
    }


    // this one is optional - refer to Slim3 - Simplifying Routing At Scale
    // https://blog.programster.org/slim3-simplifying-routing-at-scale
    abstract public static function registerRoutes(\Slim\App $app) : void;
}
