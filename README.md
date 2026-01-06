# PHP Trino Client

A simple, reusable PHP client library for [Trino](https://trino.io/) that supports:

- Sync queries
- Streaming large datasets (memory-efficient)
- Async query execution
- Optional authentication (Basic / Bearer JWT)
- Configurable catalog, schema, timeout

> Designed for PHP 8+, lightweight, and easy to integrate into any project.

---

## Installation

Use Composer to install:

```bash
composer require coodde/php-trino-client
```

## Features

- Lightweight, PSR-4 autoloaded
- Exception handling via TrinoException
- Configurable catalog, schema, timeout
- Sync + streaming + async query execution
- Optional authentication

## Examples

You can find sample code in the directory called "examples"

