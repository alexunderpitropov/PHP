<?php
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/controllers/RecipeController.php';

define('APP_URL', 'http://localhost:8080/');

$controller = new RecipeController();
$route = $_GET['route'] ?? 'home';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

$routes = [
    'home' => fn() => $controller->listRecipes(),
    'create' => fn() => $controller->addRecipeForm(),
    'save' => fn() => $controller->saveRecipe(),
    'show' => fn() => $id ? $controller->viewRecipe($id) : redirect(APP_URL),
    'edit' => fn() => $id ? $controller->editRecipeForm($id) : redirect(APP_URL),
    'update' => fn() => $id ? $controller->updateRecipe($id) : redirect(APP_URL),
    'delete' => fn() => $id ? $controller->deleteRecipe($id) : redirect(APP_URL),
];

if (isset($routes[$route])) {
    $routes[$route]();
} else {
    $controller->listRecipes();
}