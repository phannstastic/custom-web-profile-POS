<?php

namespace App\Models;

class Product extends Model
{
    protected string $table = 'products';

    public function getAllWithCategory(): array
    {
        $stmt = static::getDb()->query("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.is_available DESC, c.id ASC, p.id ASC
        ");
        return $stmt->fetchAll();
    }

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

    public function create(array $data): bool
    {
        $name = trim($data['name']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $baseSlug = $slug;
        $counter = 1;
        while ($this->findBySlug($slug)) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $stmt = static::getDb()->prepare("
            INSERT INTO products (category_id, name, slug, description, price, image, gallery_images, is_available, is_featured)
            VALUES (:category_id, :name, :slug, :description, :price, :image, :gallery_images, :is_available, :is_featured)
        ");

        return $stmt->execute([
            ':category_id'     => !empty($data['category_id']) ? (int) $data['category_id'] : null,
            ':name'            => $name,
            ':slug'            => $slug,
            ':description'     => $data['description'] ?? null,
            ':price'           => (int) ($data['price'] ?? 0),
            ':image'           => $data['image'] ?? null,
            ':gallery_images'  => $data['gallery_images'] ?? null,
            ':is_available'    => isset($data['is_available']) ? (int) $data['is_available'] : 1,
            ':is_featured'     => isset($data['is_featured']) ? (int) $data['is_featured'] : 0,
        ]);
    }

    public function updateProduct(int $id, array $data): bool
    {
        $name = trim($data['name']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        $sql = "
            UPDATE products 
            SET category_id = :category_id,
                name = :name,
                slug = :slug,
                description = :description,
                price = :price,
                is_available = :is_available,
                is_featured = :is_featured,
                updated_at = CURRENT_TIMESTAMP
        ";

        $params = [
            ':category_id'  => !empty($data['category_id']) ? (int) $data['category_id'] : null,
            ':name'         => $name,
            ':slug'         => $slug,
            ':description'  => $data['description'] ?? null,
            ':price'        => (int) ($data['price'] ?? 0),
            ':is_available' => isset($data['is_available']) ? (int) $data['is_available'] : 1,
            ':is_featured'  => isset($data['is_featured']) ? (int) $data['is_featured'] : 0,
            ':id'           => $id
        ];

        if (!empty($data['image'])) {
            $sql .= ", image = :image ";
            $params[':image'] = $data['image'];
        }

        if (isset($data['gallery_images'])) {
            $sql .= ", gallery_images = :gallery_images ";
            $params[':gallery_images'] = $data['gallery_images'];
        }

        $sql .= " WHERE id = :id";

        $stmt = static::getDb()->prepare($sql);
        return $stmt->execute($params);
    }

    public function toggleAvailability(int $id): bool
    {
        $stmt = static::getDb()->prepare("
            UPDATE products 
            SET is_available = CASE WHEN is_available = 1 THEN 0 ELSE 1 END,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    public function toggleFeatured(int $id): bool
    {
        $stmt = static::getDb()->prepare("
            UPDATE products 
            SET is_featured = CASE WHEN is_featured = 1 THEN 0 ELSE 1 END,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = static::getDb()->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = static::getDb()->prepare("SELECT * FROM products WHERE slug = :slug LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
