<?php

namespace App\Controllers;

abstract class Controller
{
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = dirname(__DIR__, 2) . "/views/{$view}.php";

        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo "View [{$view}] not found.";
            exit;
        }

        require $viewFile;
        exit;
    }
}
