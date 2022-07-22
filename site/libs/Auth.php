<?php

class Auth
{
    /**
     * Specify here the names of all routes that are public and do not require being logged in.
     * @return array
     */
    public static function getPublicRoutes() : array
    {
        return [
            //'login',
        ];
    }


    public static function isLoggedIn() : bool
    {
        // specify your logic here for determining if the user is logged in. E.g. checking a session variable etc.
        return true;
    }
}
