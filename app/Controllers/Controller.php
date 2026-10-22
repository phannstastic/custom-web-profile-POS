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

    protected function render(string $view, array $data = [], ?string $layout = null): void
    {
        extract($data);
        $viewFile = dirname(__DIR__, 2) . "/views/{$view}.php";

        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo "View [{$view}] not found.";
            exit;
        }

        if ($layout !== null) {
            $layoutFile = dirname(__DIR__, 2) . "/views/{$layout}.php";
            if (file_exists($layoutFile)) {
                ob_start();
                require $viewFile;
                $content = ob_get_clean();
                require $layoutFile;
                exit;
            }
        }

        require $viewFile;
        exit;
    }

    protected function getSettings(): array
    {
        $db = \App\Database\Connection::get();
        $settingsRaw = $db->query("SELECT key, value FROM settings")->fetchAll();
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }
}
