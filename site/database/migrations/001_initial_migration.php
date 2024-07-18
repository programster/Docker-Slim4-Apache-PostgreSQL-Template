<?php

/*
 * Fill in this initial migration to get your database set up...
 */

use Programster\PgsqlLib\PgSqlConnection;


class InitialMigration implements Programster\PgsqlMigrations\MigrationInterface
{
    /**
     * @param $connectionResource
     * @return void
     */
    public function up($connectionResource): void
    {
        $db = new PgSqlConnection($connectionResource);

        $query =
            'CREATE TABLE "user" (
                "id" uuid NOT NULL PRIMARY KEY,
                "first_name" varchar(255) NOT NULL,
                "last_name" varchar(255) NOT NULL,
                "email" varchar(255) NOT NULL
            )';

        $result = $db->query($query);
    }


    public function down($connectionResource): void
    {
        $db = new PgSqlConnection($connectionResource);
        $query = 'DROP TABLE "user"';
        $result = $db->query($query);
    }
}
