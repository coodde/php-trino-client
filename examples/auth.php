<?php

$config = new \TrinoClient\Config(
    'https://trino-secure:8443',
    'php_user',
    'hive',
    'default',
    30,
    'bearer',
    'YOUR_JWT_TOKEN'
);

$client = new \TrinoClient\Client($config);

