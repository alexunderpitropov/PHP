<?php
require_once __DIR__ . '/../models/Recipe.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../helpers.php';
session_start();

class RecipeController {
    private Recipe $recipe;
    private Category $category;

    public function __construct() {
        $this->recipe = new Recipe();
        $this->category = new Category();
    }

    public function listRecipes(): void {
        $page = max(1, filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT));
        $perPage = 5;
        $recipes = $this->recipe->fetchAll($page, $perPage);
        $totalPages = ceil($this->recipe->countAll() / $perPage);
        $categories = array_column($this->category->fetchAll(), null, 'id');
        require __DIR__ . '/../../templates/index.php';
    }

    public function addRecipeForm(): void {
        $categories = $this->category->fetchAll();
        $errors = [];
        $csrfToken = generateCsrfToken();
        require __DIR__ . '/../../templates/recipe/create.php';
    }

    public function saveRecipe(): void {
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            die('Invalid CSRF token');
        }
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'category_id' => filter_var($_POST['category_id'] ?? '', FILTER_VALIDATE_INT),
            'ingredients' => trim($_POST['ingredients'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'steps' => trim($_POST['steps'] ?? ''),
        ];
        $errors = $this->validateRecipe($data);

        if (!$errors && $this->recipe->add($data)) {
            redirect(APP_URL);
        } else {
            $errors['general'] = $errors['general'] ?? 'Failed to save recipe';
            $categories = $this->category->fetchAll();
            $csrfToken = generateCsrfToken();
            require __DIR__ . '/../../templates/recipe/create.php';
        }
    }

    public function viewRecipe(int $id): void {
        $recipe = $this->recipe->getById($id);
        if (!$recipe) {
            die('Recipe not found');
        }
        $category = $this->category->getById($recipe['category_id']);
        $csrfToken = generateCsrfToken();
        require __DIR__ . '/../../templates/recipe/show.php';
    }

    public function editRecipeForm(int $id): void {
        $recipe = $this->recipe->getById($id);
        if (!$recipe) {
            die('Recipe not found');
        }
        $categories = $this->category->fetchAll();
        $errors = [];
        $csrfToken = generateCsrfToken();
        require __DIR__ . '/../../templates/recipe/edit.php';
    }

    public function updateRecipe(int $id): void {
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            die('Invalid CSRF token');
        }
        $recipe = $this->recipe->getById($id);
        if (!$recipe) {
            die('Recipe not found');
        }
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'category_id' => filter_var($_POST['category_id'] ?? '', FILTER_VALIDATE_INT),
            'ingredients' => trim($_POST['ingredients'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'steps' => trim($_POST['steps'] ?? ''),
        ];
        $errors = $this->validateRecipe($data);

        if (!$errors && $this->recipe->modify($id, $data)) {
            redirect(APP_URL . "?route=show&id=$id");
        } else {
            $errors['general'] = $errors['general'] ?? 'Failed to update recipe';
            $categories = $this->category->fetchAll();
            $csrfToken = generateCsrfToken();
            require __DIR__ . '/../../templates/recipe/edit.php';
        }
    }

    public function deleteRecipe(int $id): void {
        if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
            die('Invalid CSRF token');
        }
        if ($this->recipe->getById($id)) {
            $this->recipe->remove($id);
        }
        redirect(APP_URL);
    }

    private function validateRecipe(array $data): array {
        $errors = [];
        if (empty($data['title']) || strlen($data['title']) < 3) {
            $errors['title'] = 'Title must be at least 3 characters';
        }
        if (!$data['category_id'] || !$this->category->getById($data['category_id'])) {
            $errors['category_id'] = 'Select a valid category';
        }
        if (empty($data['ingredients'])) {
            $errors['ingredients'] = 'Ingredients are required';
        }
        if (empty($data['steps'])) {
            $errors['steps'] = 'Steps are required';
        }
        return $errors;
    }
}