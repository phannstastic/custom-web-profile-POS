<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

abstract class Model
{
    protected static ?PDO $db = null;
    protected string $table;

    public function __construct()
    {
        if (static::$db === null) {
            static::$db = Connection::get();
        }
    }

    public static function getDb(): PDO
    {
        if (static::$db === null) {
            static::$db = Connection::get();
        }
        return static::$db;
    }

    public function all(): array
    {
        $stmt = static::getDb()->query("SELECT * FROM {$this->table} ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = static::getDb()->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
