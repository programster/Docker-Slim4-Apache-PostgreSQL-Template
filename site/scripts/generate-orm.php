<?php

/*
 * This script will update the ORM models to reflect the strucuture of the database.
 */

require_once(__DIR__ . '/../bootstrap.php');
$db = DB::getConnection();

$generator = new \Programster\OrmGenerator\PgSqlGenerator($db->getResource(), __DIR__ . '/../models/orm');
$generator->run();
