<?php
namespace TrinoClient;

class Query
{
    public string $sql;
    public ?string $nextUri = null;
    public array $data = [];

    public function __construct(string $sql)
    {
        $this->sql = $sql;
    }
}

