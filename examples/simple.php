<?php

$config = new \TrinoClient\Config(
    'http://trino-server:8080',
    'php_user',
    'hive',
    'default',
    30
);

$client = new \TrinoClient\Client($config);

$rows = $client->query('SELECT * FROM my_table LIMIT 10');
print_r($rows);

