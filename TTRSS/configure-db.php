#!/usr/bin/env php
<?php

$config = array();

// path to ttrss
$config['TTRSS_SELF_URL_PATH'] = env('TTRSS_SELF_URL_PATH', 'http://localhost');

if (getenv('TTRSS_DB_TYPE') !== false) {
    $config['TTRSS_DB_TYPE'] = getenv('TTRSS_DB_TYPE');
} elseif (getenv('TTRSS_DB_PORT_5432_TCP_ADDR') !== false) {
    // postgres container linked
    $config['TTRSS_DB_TYPE'] = 'pgsql';
    $eport = 5432;
} elseif (getenv('TTRSS_DB_PORT_3306_TCP_ADDR') !== false) {
    // mysql container linked
    $config['TTRSS_DB_TYPE'] = 'mysql';
    $eport = 3306;
}

if (!empty($eport)) {
    $config['TTRSS_DB_HOST'] = env('TTRSS_DB_PORT_' . $eport . '_TCP_ADDR');
    $config['TTRSS_DB_PORT'] = env('TTRSS_DB_PORT_' . $eport . '_TCP_PORT');
} elseif (getenv('TTRSS_DB_PORT') === false) {
    error('The env TTRSS_DB_PORT does not exist. Make sure to run with "--link mypostgresinstance:DB"');
} elseif (is_numeric(getenv('TTRSS_DB_PORT')) && getenv('TTRSS_DB_HOST') !== false) {
    // numeric TTRSS_DB_PORT provided; assume port number passed directly
    $config['TTRSS_DB_HOST'] = env('TTRSS_DB_HOST');
    $config['TTRSS_DB_PORT'] = env('TTRSS_DB_PORT');

    if (empty($config['TTRSS_DB_TYPE'])) {
        switch ($config['TTRSS_DB_PORT']) {
            case 3306:
                $config['TTRSS_DB_TYPE'] = 'mysql';
                break;
            case 5432:
                $config['TTRSS_DB_TYPE'] = 'pgsql';
                break;
            default:
                error('Database on non-standard port ' . $config['TTRSS_DB_PORT'] . ' and env TTRSS_DB_TYPE not present');
        }
    }
}

// database credentials for this instance
//   database name (DB_NAME) can be supplied or detaults to "ttrss"
//   database user (DB_USER) can be supplied or defaults to database name
//   database pass (DB_PASS) can be supplied or defaults to database user
$config['TTRSS_DB_NAME'] = env('TTRSS_DB_NAME', 'ttrss');
$config['TTRSS_DB_USER'] = env('TTRSS_DB_USER', $config['TTRSS_DB_NAME']);
$config['TTRSS_DB_PASS'] = env('TTRSS_DB_PASS', $config['TTRSS_DB_USER']);

if (!dbcheck($config)) {
    echo 'Database login failed, trying to create...' . PHP_EOL;
    // superuser account to create new database and corresponding user account
    //   username (SU_USER) can be supplied or defaults to "docker"
    //   password (SU_PASS) can be supplied or defaults to username

    $super = $config;

    $super['TTRSS_DB_NAME'] = null;
    $super['TTRSS_DB_USER'] = env('TTRSS_DB_ENV_USER', 'docker');
    $super['TTRSS_DB_PASS'] = env('TTRSS_DB_ENV_PASS', $super['TTRSS_DB_USER']);
    
    $pdo = dbconnect($super);

    if ($super['TTRSS_DB_TYPE'] === 'mysql') {
        $pdo->exec('CREATE DATABASE ' . ($config['TTRSS_DB_NAME']));
        $pdo->exec('GRANT ALL PRIVILEGES ON ' . ($config['TTRSS_DB_NAME']) . '.* TO ' . $pdo->quote($config['TTRSS_DB_USER']) . '@"%" IDENTIFIED BY ' . $pdo->quote($config['TTRSS_DB_PASS']));
    } else {
        $pdo->exec('CREATE ROLE ' . ($config['TTRSS_DB_USER']) . ' WITH LOGIN PASSWORD ' . $pdo->quote($config['TTRSS_DB_PASS']));
        $pdo->exec('CREATE DATABASE ' . ($config['TTRSS_DB_NAME']) . ' WITH OWNER ' . ($config['TTRSS_DB_USER']));
    }

    unset($pdo);
    
    if (dbcheck($config)) {
        echo 'Database login created and confirmed' . PHP_EOL;
    } else {
        error('Database login failed, trying to create login failed as well');
    }
}

$pdo = dbconnect($config);
try {
    $pdo->query('SELECT 1 FROM ttrss_feeds');
    // reached this point => table found, assume db is complete
}
catch (PDOException $e) {
    echo 'Database table not found, applying schema... ' . PHP_EOL;
    $schema = file_get_contents('schema/ttrss_schema_' . $config['TTRSS_DB_TYPE'] . '.sql');
    $schema = preg_replace('/--(.*?);/', '', $schema);
    $schema = preg_replace('/[\r\n]/', ' ', $schema);
    $schema = trim($schema, ' ;');
    foreach (explode(';', $schema) as $stm) {
        $pdo->exec($stm);
    }
    unset($pdo);
}

function env($name, $default = null)
{
    $v = getenv($name) ?: $default;
    
    if ($v === null) {
        error('The env ' . $name . ' does not exist');
    }
    
    return $v;
}

function error($text)
{
    echo 'Error: ' . $text . PHP_EOL;
    exit(1);
}

function dbconnect($config)
{
    $map = array('host' => 'HOST', 'port' => 'PORT', 'dbname' => 'NAME');
    $dsn = $config['TTRSS_DB_TYPE'] . ':';
    foreach ($map as $d => $h) {
        if (isset($config['TTRSS_DB_' . $h])) {
            $dsn .= $d . '=' . $config['TTRSS_DB_' . $h] . ';';
        }
    }
    $pdo = new \PDO($dsn, $config['TTRSS_DB_USER'], $config['TTRSS_DB_PASS']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

function dbcheck($config)
{
    try {
        dbconnect($config);
        return true;
    }
    catch (PDOException $e) {
        return false;
    }
}

