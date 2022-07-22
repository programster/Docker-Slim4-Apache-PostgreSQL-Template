<?php

use Programster\PgsqlLib\PgSqlConnection;

class DB
{
    /**
     * Fetches the connection to the PostgreSQL database. If the connection has already been
     * created, the existing instance is returned.
     * @return PgSqlConnection
     */
    public static function getConnection() : PgSqlConnection
    {
        static $db = null;

        if ($db === null)
        {
            $db = PgSqlConnection::create(
                DB_HOST,
                DB_NAME,
                DB_USER,
                DB_PASSWORD
            );
        }

        return $db;
    }
}
