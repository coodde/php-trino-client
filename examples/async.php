<?php

$config = new \TrinoClient\Config(
    'http://trino-server:8080',
    'php_user',
    'hive',
    'default',
    30
);

$client = new \TrinoClient\Client($config);

$nextUri = $client->startAsyncQuery('SELECT COUNT(*) FROM huge_table');
foreach ($client->pollAsync($nextUri) as $row) {
    print_r($row);
}

