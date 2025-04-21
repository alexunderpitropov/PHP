<?php
require_once __DIR__ . '/../db.php';

class Recipe {
    private PDO $db;

    public function __construct() {
        $this->db = connectToDatabase();
    }

    public function fetchAll(int $page = 1, int $perPage = 5): array {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare('SELECT * FROM recipes ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll(): int {
        return (int)$this->db->query('SELECT COUNT(*) FROM recipes')->fetchColumn();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM recipes WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function add(array $data): bool {
        $stmt = $this->db->prepare('
            INSERT INTO recipes (title, category_id, ingredients, description, steps)
            VALUES (?, ?, ?, ?, ?)
        ');
        return $stmt->execute([
            $data['title'],
            $data['category_id'],
            $data['ingredients'],
            $data['description'],
            $data['steps'],
        ]);
    }

    public function modify(int $id, array $data): bool {
        $stmt = $this->db->prepare('
            UPDATE recipes
            SET title = ?, category_id = ?, ingredients = ?, description = ?, steps = ?
            WHERE id = ?
        ');
        return $stmt->execute([
            $data['title'],
            $data['category_id'],
            $data['ingredients'],
            $data['description'],
            $data['steps'],
            $id,
        ]);
    }

    public function remove(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM recipes WHERE id = ?');
        return $stmt->execute([$id]);
    }
}