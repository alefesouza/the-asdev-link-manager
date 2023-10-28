<?php

$env = parse_ini_file('.env');

$username = $env['INSERT_USERNAME'];
$password = $env['INSERT_PASSWORD'];

$db_host     = $env['DB_HOST'];
$db_name     = $env['DB_DATABASE'];
$db_login    = $env['DB_USERNAME'];
$db_password = $env['DB_PASSWORD'];

$db = new PDO( "mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_login, $db_password );
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
