<?php

/*
 * This class represents a single row in a table. E.g. each row in a table can
 * be turned into one of these and interacted with.
 */

declare(strict_types = 1);



class User extends \Programster\PgsqlObjects\AbstractTableRowObject
{
    private string $m_firstName;
    private string $m_lastName;
    private string $m_email;


    public function getTableHandler(): \Programster\PgsqlObjects\TableInterface
    {
        return UserTable::getInstance();
    }

    protected function getAccessorFunctions(): array
    {
        return [
            'first_name' => function() { return $this->m_firstName; },
            'last_name' => function() { return $this->m_lastName; },
            'email' => function() { return $this->m_email; },
        ];
    }

    protected function getSetFunctions(): array
    {
        return [
            'first_name' => function($x) { $this->m_firstName = $x; },
            'last_name' => function($x) { $this->m_lastName = $x; },
            'email' => function($x) { $this->m_email = $x; },
        ];
    }
}
