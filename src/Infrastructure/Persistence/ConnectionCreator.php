<?php

namespace Alura\Pdo\Infrastructure\Persistence;

use PDO;

class ConnectionCreator
{
    public static function createConnection(): PDO
    {
        $caminhoAbsoluto = __DIR__ . '/../../../banco.sqlite';
        return new PDO('sqlite:' . $caminhoAbsoluto);
    }
}

// 21:55
