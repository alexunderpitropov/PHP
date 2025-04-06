# Лабораторная работа №4. Обработка и валидация форм

## Цель работы
Освоить основные принципы работы с HTML-формами в PHP, включая отправку данных на сервер и их обработку, включая валидацию данных.

## Тема проекта
Каталог рецептов — веб-приложение, позволяющее добавлять и просматривать рецепты. Данные сохраняются в текстовый файл в формате JSON.

## Структура проекта
```
recipe-book/
├── public/                        
│   ├── index.php                   # Главная страница (вывод последних рецептов)
│   └── recipe/                    
│       ├── create.php              # Форма добавления рецепта
│       └── index.php               # Страница с отображением всех рецептов с пагинацией
│   └── handlers/
│       └── create_recipe_handlers # Обработка формы добавления рецепта
├── src/                            
│   └── helpers.php                 # Вспомогательные функции (валидация, фильтрация)
├── storage/                        
│   └── recipes.txt                 # Файл для хранения рецептов (по одному JSON на строку)
└── README.md                       # Описание проекта
```

## Реализация

### Задание 1: Создание проекта
Проект создан в директории `recipe-book`, реализована файловая структура согласно заданию.

### Задание 2: Форма добавления рецепта
Форма реализована в `public/recipe/create.php`. Она включает следующие поля:
- Название рецепта (`<input type="text">`)
- Категория (`<select>`)
- Ингредиенты (`<textarea>`)
- Описание (`<textarea>`)
- Теги (`<select multiple>`) с возможностью множественного выбора
- Шаги приготовления (`<textarea>`)
- Кнопка отправки (`<button type="submit">`)

Пример HTML-фрагмента:
```php
<form action="/handlers/create_recipe_handlers" method="POST">
    <input type="text" name="title" placeholder="Название" required>
    <select name="category">
        <option value="Завтрак">Завтрак</option>
        <option value="Обед">Обед</option>
        <option value="Ужин">Ужин</option>
    </select>
    <textarea name="ingredients" placeholder="Ингредиенты" required></textarea>
    <textarea name="description" placeholder="Описание" required></textarea>
    <select name="tags[]" multiple>
        <option value="Быстро">Быстро</option>
        <option value="Вегетарианское">Вегетарианское</option>
    </select>
    <textarea name="steps" placeholder="Шаги приготовления" required></textarea>
    <button type="submit">Добавить рецепт</button>
</form>
```

### Задание 3: Обработка формы
Файл `public/handlers/create_recipe_handlers` обрабатывает отправку формы:
- Фильтрация данных (`sanitizeInput`)
- Валидация данных (`validateRecipe`)
- Сохранение рецепта в файл `storage/recipes.txt` в формате JSON (одна строка — один рецепт):

```php
$recipe = [
    'title' => sanitizeInput($_POST['title']),
    'category' => sanitizeInput($_POST['category']),
    'ingredients' => sanitizeInput($_POST['ingredients']),
    'description' => sanitizeInput($_POST['description']),
    'tags' => $_POST['tags'],
    'steps' => sanitizeInput($_POST['steps']),
    'created_at' => date('Y-m-d H:i:s')
];
file_put_contents('../../storage/recipes.txt', json_encode($recipe) . PHP_EOL, FILE_APPEND);
```

- Если валидация не пройдена — ошибки сохраняются в сессию, и пользователь перенаправляется обратно на форму.

### Задание 4: Отображение рецептов
#### Главная страница `public/index.php`
- Считываются строки из `storage/recipes.txt`, декодируются JSON и выводятся два последних рецепта:

```php
$lines = file('../storage/recipes.txt');
$recipes = array_reverse(array_slice($lines, -2));
foreach ($recipes as $line) {
    $recipe = json_decode($line, true);
    echo '<h2>' . htmlspecialchars($recipe['title']) . '</h2>';
    echo '<p>' . nl2br(htmlspecialchars($recipe['description'])) . '</p>';
}
```

#### Страница всех рецептов `public/recipe/index.php`
- Загружаются все рецепты, реализована пагинация по 5 рецептов на страницу:

```php
$lines = file('../../storage/recipes.txt');
$total = count($lines);
$perPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $perPage;
$recipes = array_slice($lines, $start, $perPage);
foreach ($recipes as $line) {
    $recipe = json_decode($line, true);
    echo '<h2>' . htmlspecialchars($recipe['title']) . '</h2>';
    echo '<p>' . nl2br(htmlspecialchars($recipe['description'])) . '</p>';
}
```

### Дополнительное задание: Пагинация
- Реализована навигация по страницам:

```php
for ($i = 1; $i <= ceil($total / $perPage); $i++) {
    echo '<a href="?page=' . $i . '">' . $i . '</a> ';
}
```

## Контрольные вопросы

1. **Какие методы HTTP применяются для отправки данных формы?**
   - Применяются методы `GET` и `POST`. В этой лабораторной работе используется `POST`, так как форма отправляет чувствительные данные (рецепты), которые не должны отображаться в URL.

2. **Что такое валидация данных, и чем она отличается от фильтрации?**
   - *Фильтрация* — это процесс очистки данных от лишних символов и пробелов (например, `trim`, `htmlspecialchars`).
   - *Валидация* — это проверка, соответствуют ли данные определённым правилам (например, обязательные поля не пустые, длина строки и т.п.).

3. **Какие функции PHP используются для фильтрации данных?**
   - Стандартные функции: `trim()`, `htmlspecialchars()`.
   - А также пользовательская функция `sanitizeInput()` из `helpers.php`, которая комбинирует обе.

---

## Вывод

В результате лабораторной работы был создан полноценный мини-каталог рецептов. Реализована форма для ввода данных, серверная обработка с фильтрацией и валидацией, сохранение в файл и отображение рецептов с пагинацией. Работа показала, как важно валидировать данные при работе с пользовательским вводом и как эффективно использовать файловое хранение JSON в небольших проектах на PHP.

