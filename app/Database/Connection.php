<?php

namespace App\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    public static function get(?string $dbPath = null): PDO
    {
        if (self::$instance === null) {
            $path = $dbPath ?? dirname(__DIR__, 2) . '/database/database.sqlite';
            $dir = dirname($path);

            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            try {
                self::$instance = new PDO("sqlite:" . $path);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                // Aktifkan foreign key constraint pada SQLite
                self::$instance->exec("PRAGMA foreign_keys = ON;");
            } catch (PDOException $e) {
                die("Koneksi Database Gagal: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }
}
