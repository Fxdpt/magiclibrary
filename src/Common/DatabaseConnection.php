<?php

namespace MagicLibrary\Common;

final class DatabaseConnection extends \PDO
{
    public function __construct()
    {
        parent::__construct($_ENV['DATABASE_DSN'], $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD']);
    }
}
