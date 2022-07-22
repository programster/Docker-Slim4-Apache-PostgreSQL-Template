<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(__DIR__ . '/../bootstrap.php');

$migrationManager = new \Programster\PgsqlMigrations\MigrationManager(
    __DIR__ . '/../database/migrations',
    DB::getConnection()->getResource()
);

print "Running migrations..." . PHP_EOL;
$migrationManager->migrate();
print "Migrations complete!" . PHP_EOL;
