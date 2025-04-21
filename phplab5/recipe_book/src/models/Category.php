<?php
require_once __DIR__ . '/../db.php';

class Category {
    private PDO $db;

    public function __construct() {
        $this->db = connectToDatabase();
    }

    public function fetchAll(): array {
        $stmt = $this->db->query('SELECT * FROM categories ORDER BY name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}