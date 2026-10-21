<?php

namespace App\Database;

use PDO;

class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        self::startSession();
        if (!self::check()) {
            return null;
        }

        return [
            'id'       => $_SESSION['user_id'],
            'name'     => $_SESSION['user_name'] ?? 'Admin',
            'username' => $_SESSION['user_username'] ?? 'admin',
            'role'     => $_SESSION['user_role'] ?? 'admin',
        ];
    }

    public static function attempt(string $username, string $password): bool
    {
        self::startSession();
        $db = Connection::get();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        self::startSession();
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_username'], $_SESSION['user_role']);
        session_destroy();
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: /admin/login');
            exit;
        }
    }
}
