<?php

/*
 * This is a really simple/basic script that has to do just one job. Keep trying to get a database connection until
 * one becomes available. This allows us to block startup of the site until the database is up and running.
 */

require_once(__DIR__ . '/../bootstrap.php');


$connected = false;

while ($connected === false)
{
    try
    {
        $db = @\Programster\PgsqlLib\PgSqlConnection::create(
            DB_HOST,
            DB_NAME,
            DB_USER,
            DB_PASSWORD
        );

        $connected = true;
    }
    catch (Exception)
    {
        print "Database still warming up. Waiting for it to come online..." . PHP_EOL;

        // wait for a bit to give the database time to spin up. No point wasting CPU.
        sleep(1);
    }
}



