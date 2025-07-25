<?php

/*
 * Fill in this initial migration to get your database set up...
 */

use Programster\PgsqlLib\PgSqlConnection;


class InitialMigration implements Programster\PgsqlMigrations\MigrationInterface
{
    /**
     * Run queries for upgrading the database version.
     * @throws \Programster\PgsqlLib\Exceptions\ExceptionQueryError - if there is an issue with the query.
     * @throws \Programster\PgsqlLib\Exceptions\ExceptionConnectionError - if database is not actually connected.
     */
    public function up(\PgSql\Connection $connectionResource): void
    {
        $db = new PgSqlConnection($connectionResource);

        $query =
            'CREATE TABLE "user" (
                "id" uuid NOT NULL PRIMARY KEY,
                "first_name" varchar(255) NOT NULL,
                "last_name" varchar(255) NOT NULL,
                "email" varchar(255) NOT NULL
            )';

        $db->query($query);
    }


    /**
     * Run queries for downgrading the database version.
     * @throws \Programster\PgsqlLib\Exceptions\ExceptionQueryError - if there is an issue with the query.
     * * @throws \Programster\PgsqlLib\Exceptions\ExceptionConnectionError - if database is not actually connected.
 */
    public function down(\PgSql\Connection $connectionResource): void
    {
        $db = new PgSqlConnection($connectionResource);
        $db->query('DROP TABLE "user"');
    }
}
