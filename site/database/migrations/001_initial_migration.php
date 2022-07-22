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
            'CREATE TABLE "MyTableName" (
                id uuid NOT NULL PRIMARY KEY,
                "my_column_name" varchar(255) NOT NULL
            )';

        $result = $db->query($query);
    }


    public function down($connectionResource): void
    {
        $db = new PgSqlConnection($connectionResource);
        $query = 'DROP TABLE "MyTableName"';
        $result = $db->query($query);
    }
}
