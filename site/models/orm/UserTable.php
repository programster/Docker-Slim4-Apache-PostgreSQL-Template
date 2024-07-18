<?php

/*
 * A class to represent a table in the database. Use this for the loading/deleting of objects/rows.
 */

declare(strict_types = 1);

use Programster\PgsqlLib\Conjunction;
use Programster\PgsqlLib\PgsqlLib;
use Programster\PgsqlLib\PgSqlConnection;
use Programster\PgsqlObjects\AbstractTable;


class UserTable extends AbstractTable
{
    public function getObjectClassName(): string
    {
        return User::class;
    }

    public function getDb(): PgSqlConnection
    {
        return DB::getConnection();
    }

    public function getFieldsThatAllowNull(): array
    {
        return [];
    }

    public function getFieldsThatHaveDefaults(): array
    {
        return [];
    }

    public function getTableName(): string
    {
        return "user";
    }

    public function generateId(): mixed
    {
        return \Programster\PgsqlObjects\Utils::generateUuid();
    }

    public function isIdGeneratedInDatabase(): bool
    {
        return false;
    }
}
