<?php

namespace App\Models;

class Category extends Model
{
    protected string $table = 'categories';

    public function allOrdered(): array
    {
        $stmt = static::getDb()->query("SELECT * FROM categories ORDER BY id ASC");
        return $stmt->fetchAll();
    }
}
