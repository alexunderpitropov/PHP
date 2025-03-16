# Лабораторная работа №3. Массивы и Функции

## Цель работы

Овладеть работой с массивами в PHP, выполняя следующие операции:
- Создание и обработка массивов.
- Добавление, удаление, сортировка и поиск элементов в массиве.
- Использование функций, включая анонимные функции и работу с аргументами.
- Работа с файловой системой: считывание и отображение изображений.

---

## Задание 1. Работа с массивами

#### Объявление строгой типизации

```php
declare(strict_types=1);
```


### Основные операции с массивами

Исходный массив транзакций:
```php
$transactions = [
    [
        "id" => 1,
        "date" => "2019-01-01",
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "SuperMart",
    ],
    [
        "id" => 2,
        "date" => "2020-02-15",
        "amount" => 75.50,
        "description" => "Dinner with friends",
        "merchant" => "Local Restaurant",
    ],
];
```

### Вывод списка транзакций
Функция для отображения массива транзакций в виде HTML-таблицы:
```php
function displayTransactions(array $transactions): void {
    echo "<link rel='stylesheet' href='styles.css'>";
    echo "<table>";
    echo "<thead><tr><th>ID</th><th>Дата</th><th>Сумма</th><th>Описание</th><th>Магазин</th><th>Дней с транзакции</th></tr></thead>";
    echo "<tbody>";
    foreach ($transactions as $transaction) {
        $days = daysSinceTransaction($transaction['date']);
        echo "<tr>";
        echo "<td>{$transaction['id']}</td>";
        echo "<td>{$transaction['date']}</td>";
        echo "<td>{$transaction['amount']}</td>";
        echo "<td>{$transaction['description']}</td>";
        echo "<td>{$transaction['merchant']}</td>";
        echo "<td>{$days}</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
}
```

### Подсчет общей суммы транзакций
```php
function calculateTotalAmount(array $transactions): float {
    return array_sum(array_column($transactions, 'amount'));
}
```

### Поиск транзакции по описанию
```php
function findTransactionByDescription(array $transactions, string $descriptionPart): array {
    return array_filter($transactions, fn($t) => strpos(strtolower($t['description']), strtolower($descriptionPart)) !== false);
}
```

### Подсчет дней с момента транзакции
```php
function daysSinceTransaction(string $date): int {
    $transactionDate = new DateTime($date);
    $now = new DateTime();
    return $now->diff($transactionDate)->days;
}
```

### Сортировка по дате (от новых к старым)
```php
usort($transactions, fn($a, $b) => strcmp($b['date'], $a['date']));
```

---

## Задание 2. Работа с файловой системой

### Автоматическое отображение изображений из папки `images/`
PHP-скрипт автоматически загружает и выводит изображения из папки `images/`. Сначала определяется путь к каталогу, затем с помощью `scandir()` получается список всех файлов. После этого отбираются только `.jpg`-изображения, а системные файлы и ненужные элементы отфильтровываются. Найденные изображения выводятся с помощью HTML-тега `<img>`, создавая динамическую галерею.
```php
$dir = 'images/';
$files = scandir($dir);
if ($files !== false) {
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'jpg') {
            echo "<img src='$dir$file' alt='Game Character'>";
        }
    }
}
```

---

## Контрольные вопросы

### Что такое массивы в PHP?
Массивы в PHP — это упорядоченные структуры данных, которые позволяют хранить несколько значений в одной переменной. Они могут содержать элементы различных типов и поддерживают ассоциативные ключи.

### Каким образом можно создать массив в PHP?
Создать массив в PHP можно несколькими способами:
- Используя квадратные скобки: `$arr = [1, 2, 3];`
- Используя функцию `array()`: `$arr = array(1, 2, 3);`
- Создавая ассоциативный массив: `$arr = ["ключ1" => "значение1", "ключ2" => "значение2"]`; 

### Для чего используется цикл foreach?
Цикл `foreach` в PHP используется для перебора элементов массива. Он автоматически проходит по каждому элементу массива, что делает его удобным для работы со структурами данных без необходимости вручную управлять индексами.

---

## Вывод
В ходе выполнения лабораторной работы были изучены массивы и функции в PHP, включая их создание, изменение, сортировку и поиск данных. Реализованная система управления транзакциями позволяет добавлять, удалять и анализировать данные, а также вычислять количество дней с момента операции. Дополнительно разработан механизм автоматического вывода изображений из папки.

Освоение работы с массивами и функциями углубило понимание структур данных в PHP, что поможет при создании более сложных веб-приложений и обработке данных в серверном программировании.

## Библиография

- [Официальная документация PHP: Массивы](https://www.php.net/manual/ru/language.types.array.php)
- [Официальная документация PHP: Цикл foreach](https://www.php.net/manual/ru/control-structures.foreach.php)
- [Материалы для работы с массивами, файловыми системами, сортировкой и сравнением](https://www.php.net/manual/ru)
