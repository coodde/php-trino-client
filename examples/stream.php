<?php

$config = new \TrinoClient\Config(
    'http://trino-server:8080',
    'php_user',
    'hive',
    'default',
    30
);

$client = new \TrinoClient\Client($config);

foreach ($client->stream('SELECT * FROM big_table') as $row) {
    print_r($row); // memory-friendly
}

