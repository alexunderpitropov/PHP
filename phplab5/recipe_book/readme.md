# Лабораторная работа №5: Работа с базой данных

## Цель работы

Освоить архитектуру с единой точкой входа, подключение шаблонов для визуализации страниц, а также переход от хранения данных в файле к использованию базы данных (MySQL).

## Условия

- Реализовать архитектуру с единой точкой входа (`index.php`), обрабатывающей все входящие HTTP-запросы.
- Настроить базовую систему шаблонов с использованием файла `layout.php` и отдельных представлений для разных страниц.
- Перенести логику работы с рецептами из файловой системы в базу данных (MySQL).

## Задание 1: Подготовка среды

1. **Настройка сервера**:

   - Использовал XAMPP для работы с MySQL и PHP.
   - Установил и настроил XAMPP, запустил Apache и MySQL через XAMPP Control Panel.

2. **Создание базы данных**:

   - Создал базу данных `recipe_book` через phpMyAdmin с кодировкой `utf8mb4_unicode_ci`.

3. **Создание таблиц**:

   - Таблица `categories`:

     ```sql
     CREATE TABLE categories (
         id INT AUTO_INCREMENT PRIMARY KEY,
         name VARCHAR(100) NOT NULL UNIQUE,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```

   - Таблица `recipes`:

     ```sql
     CREATE TABLE recipes (
         id INT AUTO_INCREMENT PRIMARY KEY,
         title VARCHAR(255) NOT NULL,
         category_id INT NOT NULL,
         ingredients TEXT NOT NULL,
         description TEXT,
         steps TEXT NOT NULL,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
     ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```

   - Добавил тестовые данные:

     ```sql
     INSERT INTO categories (name) VALUES ('Breakfast'), ('Soups'), ('Bakery');
     INSERT INTO recipes (title, category_id, ingredients, description, steps) VALUES
     ('Cheese Omelette', 1, 'Eggs, cheese, milk, salt', 'Quick and hearty breakfast', '1. Beat eggs with milk\n2. Fry in a pan\n3. Add cheese'),
     ('Vegetable Soup', 2, 'Carrots, potatoes, onions, broth', 'Healthy and warm soup', '1. Boil broth\n2. Add vegetables\n3. Cook until ready');
     ```

## Задание 2: Архитектура и шаблонизация

1. **Единая точка входа**:

   - Файл `public/index.php` обрабатывает все запросы и реализует маршрутизацию:

     ```php
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
     ```

   - **Пояснение**: Все запросы обрабатываются через `index.php`. Параметр `route` определяет, какую страницу показать (например, `create`, `show`). Используется `filter_var` для безопасной обработки `id`.

2. **Шаблонизация**:

   - Базовый шаблон `templates/layout.php`:

     ```php
     <!DOCTYPE html>
     <html lang="en">
     <head>
         <meta charset="UTF-8">
         <title><?php echo safe($pageTitle ?? 'Recipes'); ?></title>
         <link rel="stylesheet" href="<?php echo APP_URL; ?>assets/style.css">
     </head>
     <body>
         <div class="container">
             <div class="sidebar">
                 <h3>Navigation</h3>
                 <a href="<?php echo APP_URL; ?>">All Recipes</a>
                 <a href="<?php echo APP_URL; ?>?route=create">New Recipe</a>
             </div>
             <div class="content">
                 <h1>Recipes</h1>
                 <?php echo $pageContent; ?>
             </div>
         </div>
         <footer>
             <p>© 2025 Recipes</p>
         </footer>
     </body>
     </html>
     ```

   - **Пояснение**: `layout.php` задаёт общую структуру страницы: боковую панель с навигацией, основное содержимое и футер. Переменная `$pageContent` подставляется из других шаблонов.

   - Шаблон списка рецептов `templates/index.php`:

     ```php
     <?php
     ob_start();
     ?>
     <h2>All Recipes</h2>
     <?php if (!$recipes): ?>
         <p>No recipes found.</p>
     <?php else: ?>
         <ul class="recipe-list">
             <?php foreach ($recipes as $recipe): ?>
                 <li>
                     <a href="<?php echo APP_URL; ?>?route=show&id=<?php echo $recipe['id']; ?>">
                         <?php echo safe($recipe['title']); ?>
                     </a>
                     (<?php echo safe($categories[$recipe['category_id']]['name']); ?>)
                 </li>
             <?php endforeach; ?>
         </ul>
         <div class="pagination">
             <?php if ($page > 1): ?>
                 <a href="<?php echo APP_URL; ?>?page=<?php echo $page - 1; ?>">« Previous</a>
             <?php endif; ?>
             <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
             <?php if ($page < $totalPages): ?>
                 <a href="<?php echo APP_URL; ?>?page=<?php echo $page + 1; ?>">Next »</a>
             <?php endif; ?>
         </div>
     <?php endif; ?>
     <?php
     $pageContent = ob_get_clean();
     $pageTitle = 'All Recipes';
     require __DIR__ . '/layout.php';
     ```

   - **Пояснение**: Этот шаблон отображает список рецептов с пагинацией. Функция `safe()` используется для экранирования данных.

## Задание 3: Подключение к базе данных

1. **Параметры подключения** (`config/db.php`):

   ```php
   <?php
   return [
       'host' => 'localhost',
       'database' => 'recipe_book',
       'username' => 'root',
       'password' => '',
   ];
   ```

   - **Пояснение**: Храню параметры подключения отдельно для удобства изменения.

2. **Функция подключения** (`src/db.php`):

   ```php
   <?php
   /**
    * Connects to the database using PDO.
    * @return PDO The PDO instance for database operations.
    * @throws PDOException If connection fails.
    */
   function connectToDatabase(): PDO {
       $config = require __DIR__ . '/../config/db.php';
       $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
       
       try {
           $pdo = new PDO($dsn, $config['username'], $config['password']);
           $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           return $pdo;
       } catch (PDOException $e) {
           die("Failed to connect to database: " . $e->getMessage());
       }
   }
   ```

   - **Пояснение**: Использую PDO с режимом исключений для обработки ошибок. Кодировка `utf8mb4` обеспечивает поддержку Unicode.

## Задание 3: Реализация CRUD-функциональности

CRUD-логика реализована в `src/controllers/RecipeController.php`. Пример метода добавления рецепта:

```php
/**
 * Displays the form for adding a new recipe.
 */
public function addRecipeForm(): void {
    $pdo = connectToDatabase();
    $stmt = $pdo->query('SELECT * FROM categories');
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $csrfToken = generateCsrfToken();
    require __DIR__ . '/../../templates/recipe/create.php';
}

/**
 * Saves a new recipe to the database.
 */
public function saveRecipe(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        redirect(APP_URL);
    }

    $title = trim($_POST['title'] ?? '');
    $categoryId = filter_var($_POST['category_id'] ?? '', FILTER_VALIDATE_INT);
    $ingredients = trim($_POST['ingredients'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $steps = trim($_POST['steps'] ?? '');

    $errors = [];
    if (empty($title)) $errors['title'] = 'Title is required';
    if (!$categoryId) $errors['category_id'] = 'Category is required';
    if (empty($ingredients)) $errors['ingredients'] = 'Ingredients are required';
    if (empty($steps)) $errors['steps'] = 'Steps are required';

    if ($errors) {
        $pdo = connectToDatabase();
        $stmt = $pdo->query('SELECT * FROM categories');
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $csrfToken = generateCsrfToken();
        require __DIR__ . '/../../templates/recipe/create.php';
        return;
    }

    $pdo = connectToDatabase();
    $stmt = $pdo->prepare('INSERT INTO recipes (title, category_id, ingredients, description, steps) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$title, $categoryId, $ingredients, $description, $steps]);
    redirect(APP_URL);
}
```

- **Пояснение**: Метод `addRecipeForm()` отображает форму, а `saveRecipe()` валидирует данные и сохраняет рецепт в базу. Использую подготовленные выражения (`prepare`, `execute`) для защиты от SQL-инъекций.

## Задание 4: Защита от SQL-инъекций

1. **Пример уязвимого кода**:

   ```php
   $id = $_GET['id'];
   $pdo->query("SELECT * FROM recipes WHERE id = $id");
   ```

   - Если `id` будет `1; DROP TABLE recipes; --`, это удалит таблицу.

2. **Защищённый код**:

   ```php
   $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
   $stmt = $pdo->prepare('SELECT * FROM recipes WHERE id = ?');
   $stmt->execute([$id]);
   ```

   - **Пояснение**: Использую подготовленные выражения и валидацию `id` через `filter_var`.

## Задание 5: Пагинация

Реализовал пагинацию в методе `listRecipes()` в `RecipeController.php`:

```php
/**
 * Lists all recipes with pagination.
 */
public function listRecipes(): void {
    $pdo = connectToDatabase();
    $perPage = 5;
    $page = max(1, filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT));
    $offset = ($page - 1) * $perPage;

    $stmt = $pdo->query('SELECT COUNT(*) FROM recipes');
    $totalRecipes = $stmt->fetchColumn();
    $totalPages = max(1, ceil($totalRecipes / $perPage));

    $stmt = $pdo->prepare('SELECT * FROM recipes LIMIT ? OFFSET ?');
    $stmt->execute([$perPage, $offset]);
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query('SELECT * FROM categories');
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $categories = array_column($categories, null, 'id');

    require __DIR__ . '/../../templates/index.php';
}
```

- **Пояснение**: Использую `LIMIT` и `OFFSET` для выборки 5 рецептов на страницу. Параметр `page` валидируется через `filter_var`.

## Проблемы и их решение

1. **Ошибка 404 при запуске через Apache**:
   - Проблема: Apache не находил `index.php`.
   - Решение: Включил `mod_rewrite` и установил `AllowOverride All` в `httpd.conf`.
2. **Ошибка подключения к базе**:
   - Проблема: `config/db.php` возвращал неправильные ключи (`dbname` вместо `database`).
   - Решение: Исправил ключи в `config/db.php`.
3. **Стили не применялись**:
   - Проблема: Неправильный путь к `style.css`.
   - Решение: Проверил `APP_URL` и путь в `layout.php`.

## Результат

Приложение позволяет:

- Просматривать список рецептов с пагинацией.
- Добавлять, редактировать и удалять рецепты.
- Защищено от SQL-инъекций через PDO.

## Контрольные вопросы

1. **Преимущества единой точки входа**:

   - Упрощает маршрутизацию и обработку запросов.
   - Повышает безопасность, так как все запросы проходят через один файл.
   - Удобно для отладки и логирования.

2. **Преимущества шаблонов**:

   - Разделение логики и представления.
   - Повторное использование кода (например, `layout.php`).
   - Упрощает поддержку и изменение дизайна.

3. **Преимущества базы данных над файлами**:

   - Быстрее доступ к данным через индексы.
   - Поддержка транзакций и целостности данных.
   - Удобство работы с большими объёмами данных.

4. **SQL-инъекция**:

   - Это атака, при которой злоумышленник вставляет вредоносный SQL-код в запрос.
   - Пример: В форме ввода `id` вводят `1; DROP TABLE recipes; --`.
   - Предотвращение: Использую подготовленные выражения (`prepare`, `execute`) и валидацию входных данных.