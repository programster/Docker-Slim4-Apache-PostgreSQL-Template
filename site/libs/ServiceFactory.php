<?php

use Programster\Log\JsonFileLogger;

class ServiceFactory
{
    public static function getLogger() : \Psr\Log\LoggerInterface
    {
        static $logger = null;

        if ($logger === null)
        {
            $logger = new JsonFileLogger("/logs/app.log");
        }

        return $logger;
    }
}
