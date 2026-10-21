<?php

namespace App\Models;

class Category extends Model
{
    protected string $table = 'categories';

    public function allOrdered(): array
    {
        $stmt = static::getDb()->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) as product_count FROM categories c ORDER BY c.id ASC");
        return $stmt->fetchAll();
    }

    public function create(string $name, ?string $icon = null): bool
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        // Ensure uniqueness
        $baseSlug = $slug;
        $counter = 1;
        while ($this->findBySlug($slug)) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $stmt = static::getDb()->prepare("INSERT INTO categories (name, slug, icon) VALUES (:name, :slug, :icon)");
        return $stmt->execute([
            ':name' => $name,
            ':slug' => $slug,
            ':icon' => $icon ?: 'fa-utensils'
        ]);
    }

    public function update(int $id, string $name, ?string $icon = null): bool
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $stmt = static::getDb()->prepare("UPDATE categories SET name = :name, slug = :slug, icon = :icon WHERE id = :id");
        return $stmt->execute([
            ':name' => $name,
            ':slug' => $slug,
            ':icon' => $icon ?: 'fa-utensils',
            ':id'   => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = static::getDb()->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = static::getDb()->prepare("SELECT * FROM categories WHERE slug = :slug LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
