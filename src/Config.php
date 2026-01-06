<?php
namespace TrinoClient;

class Config
{
    public string $url;
    public string $user;
    public ?string $catalog;
    public ?string $schema;
    public int $timeout;
    public ?string $authType; // 'basic' | 'bearer' | null
    public ?string $authToken; // password for basic, token for bearer

    public function __construct(
        string $url,
        string $user,
        ?string $catalog = null,
        ?string $schema = null,
        int $timeout = 30,
        ?string $authType = null,
        ?string $authToken = null
    ) {
        $this->url = rtrim($url, '/');
        $this->user = $user;
        $this->catalog = $catalog;
        $this->schema = $schema;
        $this->timeout = $timeout;
        $this->authType = $authType;
        $this->authToken = $authToken;
    }
}

