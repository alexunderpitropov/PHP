<?php

declare(strict_types=1);

// Исходный массив транзакций
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

/**
 * Вывод списка транзакций в HTML-таблице с подключением стилей из внешнего CSS
 */
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

/**
 * Подсчет общей суммы транзакций
 */
function calculateTotalAmount(array $transactions): float {
    return array_sum(array_column($transactions, 'amount'));
}

/**
 * Поиск транзакции по части описания
 */
function findTransactionByDescription(array $transactions, string $descriptionPart): array {
    return array_filter($transactions, fn($t) => strpos(strtolower($t['description']), strtolower($descriptionPart)) !== false);
}

/**
 * Подсчет дней с момента транзакции
 */
function daysSinceTransaction(string $date): int {
    $transactionDate = new DateTime($date);
    $now = new DateTime();
    return $now->diff($transactionDate)->days;
}

/**
 * Сортировка по дате
 */
usort($transactions, fn($a, $b) => strcmp($b['date'], $a['date']));

// Выводим таблицу с транзакциями
displayTransactions($transactions);

echo "<br><strong>Общая сумма: " . calculateTotalAmount($transactions) . "</strong>";
?>
