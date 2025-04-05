# Лабораторная работа №4. Обработка и валидация форм

## Цель работы

Цель данной лабораторной работы — освоить основные принципы работы с HTML-формами в PHP, включая отправку данных на сервер, их обработку, фильтрацию, валидацию и сохранение в файл.

Проект является основой для последующих лабораторных работ. В качестве темы выбран "Каталог рецептов".

## Структура проекта

```
recipe-book/
├── public/
│   ├── index.php                     # Главная страница с последними рецептами
│   ├── css/
│   │   └── style.css                # Стили проекта
│   └── recipe/
│       ├── create.php              # Форма добавления рецепта
│       └── index.php               # Отображение всех рецептов с пагинацией
│
├── src/
│   ├── handlers/
│   │   └── create_recipe_handler.php  # Обработка формы
│   └── helpers.php                # Вспомогательные функции
│
├── storage/
│   └── recipes.txt                # Файл хранения рецептов (JSON по строкам)
│
└── README.md                      # Описание проекта
```

## Описание работы

### Главная страница (`public/index.php`)

- Загружает все рецепты с помощью функции `loadRecipes()`
- Отображает два последних рецепта, используя `array_slice()`

```php
$recipes = loadRecipes();
$latestRecipes = array_slice($recipes, -2);
foreach ($latestRecipes as $recipe) {
    echo "<h2>" . htmlspecialchars($recipe->title) . "</h2>";
    echo "<p>" . nl2br(htmlspecialchars($recipe->description)) . "</p>";
}
```

### Страница добавления рецепта (`public/recipe/create.php`)

HTML-форма со всеми полями по заданию:

```html
<form action="/handlers/create_recipe_handler.php" method="post">
  <label>Название рецепта:
    <input type="text" name="title" required>
  </label>

  <label>Категория:
    <select name="category">
      <option>Закуски</option>
      <option>Основное блюдо</option>
      <option>Десерт</option>
    </select>
  </label>

  <label>Ингредиенты:
    <textarea name="ingredients"></textarea>
  </label>

  <label>Описание:
    <textarea name="description"></textarea>
  </label>

  <label>Теги:
    <select name="tags[]" multiple>
      <option>Легко</option>
      <option>Быстро</option>
      <option>Сытно</option>
    </select>
  </label>

  <label>Шаги приготовления:
    <textarea name="steps"></textarea>
  </label>

  <button type="submit">Отправить</button>
</form>
```

### Обработка формы (`public/handlers/create_recipe_handler.php`)

- Выполняется валидация обязательных полей
- Используется фильтрация (`trim`, `htmlspecialchars`)
- Данные сохраняются в файл построчно в JSON-формате
- В случае ошибок — возврат на форму с сообщениями об ошибках
- После сохранения — редирект на главную страницу

```php
require_once __DIR__ . '/../../src/helpers.php';
session_start();

$title = trim($_POST['title']);
$category = $_POST['category'] ?? '';
$ingredients = trim($_POST['ingredients']);
$description = trim($_POST['description']);
$tags = $_POST['tags'] ?? [];
$steps = trim($_POST['steps']);

$errors = [];
if ($title === '') $errors['title'] = 'Введите название';
if ($ingredients === '') $errors['ingredients'] = 'Введите ингредиенты';
if ($description === '') $errors['description'] = 'Введите описание';
if ($steps === '') $errors['steps'] = 'Введите шаги';

if (!empty($errors)) {
  $_SESSION['errors'] = $errors;
  header('Location: /recipe/create.php');
  exit;
}

$recipe = [
  'title' => htmlspecialchars($title),
  'category' => htmlspecialchars($category),
  'ingredients' => htmlspecialchars($ingredients),
  'description' => htmlspecialchars($description),
  'tags' => $tags,
  'steps' => htmlspecialchars($steps),
  'created_at' => date('Y-m-d H:i:s')
];

file_put_contents(__DIR__ . '/../../storage/recipes.txt', json_encode($recipe) . PHP_EOL, FILE_APPEND);
header('Location: /index.php');
exit;
```

### Вспомогательные функции (`src/helpers.php`)

```php
/**
 * Загружает все рецепты из файла.
 * @return array Массив объектов рецептов
 */
function loadRecipes(): array {
    $file = __DIR__ . '/../storage/recipes.txt';
    if (!file_exists($file)) return [];
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $recipes = array_map('json_decode', $lines);
    return array_filter($recipes);
}
```

### Пагинация (`public/recipe/index.php`)

```php
require_once __DIR__ . '/../../src/helpers.php';
$recipes = array_reverse(loadRecipes());

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5;
$offset = ($page - 1) * $perPage;

$current = array_slice($recipes, $offset, $perPage);
foreach ($current as $recipe) {
  echo "<h2>{$recipe->title}</h2>";
  echo "<p>{$recipe->description}</p>";
}
```

## Пример данных (storage/recipes.txt)

```json
{"title":"Греческий салат","category":"Закуски","ingredients":"Помидоры, огурцы","description":"Легкий и полезный","steps":"Нарезать и смешать","tags":["Легко"],"created_at":"2025-04-01 21:00:00"}
```

## Ответы на контрольные вопросы

### Какие методы HTTP применяются для отправки данных формы?

Метод **POST** используется для отправки формы на сервер, так как он позволяет безопасно передавать пользовательские данные, скрытые от пользователя в адресной строке. Он используется при добавлении новых данных (например, рецепта).

Метод **GET** применяется для передачи данных через URL. В проекте он используется для передачи номера страницы при реализации пагинации (`?page=2`). GET удобен для создания навигации и не подходит для передачи чувствительных данных.

### Что такое валидация данных и чем она отличается от фильтрации?

**Валидация** — это проверка соответствия данных определённым требованиям. Например, поле "название рецепта" не должно быть пустым, шаги должны быть заданы. Валидация предотвращает некорректный ввод и обеспечивает корректную логику приложения.

**Фильтрация** — это предварительная обработка входных данных для удаления лишних символов, пробелов, потенциально опасного HTML и JavaScript-кода. Например, функция `htmlspecialchars()` защищает от XSS-атак.

Таким образом:

- валидация отвечает на вопрос "правильно ли?"
- фильтрация — "безопасно ли сохранять/отображать?"

### Какие функции PHP используются для фильтрации данных?

- `trim()` — удаляет пробелы в начале и конце строки
- `htmlspecialchars()` — экранирует HTML-символы, предотвращая внедрение кода
- `filter_input()` — извлекает и фильтрует данные из суперглобальных массивов (`$_POST`, `$_GET` и др.)
- `filter_var()` — применяется к отдельной переменной с нужным фильтром (например, `FILTER_SANITIZE_STRING`, `FILTER_VALIDATE_EMAIL`)
- `json_encode()` и `json_decode()` — используются для безопасного сохранения и восстановления структурированных данных (массивов, объектов)

## Вывод

В ходе выполнения лабораторной работы №4 была реализована практическая система на языке PHP для добавления, хранения и отображения информации, введённой пользователем через HTML-форму. Проект «Каталог рецептов» стал базовой платформой для освоения ключевых аспектов серверной веб-разработки.

Были детально изучены и применены следующие темы:

- Работа с HTML-формами: создана форма с полным набором полей (текстовые, выпадающие списки, множественный выбор, текстовые области), охватывающих все требуемые данные рецепта.
- Обработка данных на сервере: реализован отдельный обработчик, который принимает данные формы и выполняет базовую валидацию и фильтрацию пользовательского ввода.
- Фильтрация и валидация: для защиты от потенциально опасных данных и обеспечения корректности ввода применены функции trim() и htmlspecialchars(), а также ручные проверки обязательных полей.
- Сохранение данных: все рецепты сохраняются в формате JSON, каждая запись — отдельной строкой в текстовом файле. Это позволило организовать лёгкое считывание и модификацию данных.
- Вывод информации пользователю: на главной странице отображаются два последних рецепта, а на отдельной странице предусмотрен постраничный вывод всех записей с использованием GET-параметра page.

Структура проекта: организована логичная и масштабируемая структура папок, разделены публичные файлы, обработчики и вспомогательные функции, что соответствует современным подходам к веб-разработке. Лабораторная работа дала практическое понимание жизненного цикла данных от момента их ввода пользователем до отображения и хранения на сервере. Полученные знания и готовая структура проекта позволят легко масштабировать его в следующих лабораторных — добавить авторизацию, работу с базой данных, REST API и другие функции. Таким образом, цели лабораторной работы достигнуты в полном объёме, а результат может быть использован как фундамент для полноценного веб-приложения.

## Библиография

- [PHP Manual. Forms and User Input](https://www.php.net/manual/en/tutorial.forms.php) - Официальная документация PHP, раздел, посвящённый работе с HTML-формами и обработке пользовательского ввода.
- [PHP Manual. Data Filtering](https://www.php.net/manual/en/book.filter.php) - Документация по встроенным функциям фильтрации и валидации данных с использованием `filter_input()` и `filter_var()`.
- [PHP Manual. htmlspecialchars](https://www.php.net/manual/en/function.htmlspecialchars.php) - Описание функции `htmlspecialchars`, предназначенной для защиты от XSS-атак путём экранирования специальных HTML-символов.
- [W3Schools. PHP Form Handling](https://www.w3schools.com/php/php_forms.asp) - Практическое руководство по созданию HTML-форм и их обработке в PHP с примерами кода.
- [W3Schools. PHP JSON Handling](https://www.w3schools.com/php/php_json.asp) - Информация о работе с JSON в PHP: функции `json_encode()` и `json_decode()`.
- [MDN Web Docs. HTTP methods: GET vs POST](https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods) - Сравнение HTTP-методов GET и POST, их назначение и особенности использования в веб-разработке.
