<?php

namespace App\Models;

class Product extends Model
{
    protected string $table = 'products';

    public function getActiveWithCategory(): array
    {
        $stmt = static::getDb()->query("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_available = 1
            ORDER BY c.id ASC, p.id ASC
        ");
        return $stmt->fetchAll();
    }
}
