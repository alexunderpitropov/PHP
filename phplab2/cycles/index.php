<?php
$a = 0;
$b = 0;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Циклы в PHP</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h1>Циклы в PHP</h1>

    <div class="cycle-block">
        <h3>Цикл for:</h3>
        <?php
        $a = 0;
        $b = 0;
        echo "<p><strong>Initial values:</strong> a = $a, b = $b</p>"; 
        for ($i = 0; $i <= 5; $i++) {
            $a += 10;
            $b += 5;
            echo "<p>Шаг $i: a = $a, b = $b</p>";
        }
        echo "<p><strong>End of the loop:</strong> a = $a, b = $b</p>";
        ?>
    </div>

    <div class="cycle-block">
        <h3>Цикл while:</h3>
        <?php
        $a = 0; 
        $b = 0;
        echo "<p><strong>Initial values:</strong> a = $a, b = $b</p>"; 
        $i = 0;
        while ($i <= 5) {
            $a += 10;
            $b += 5;
            echo "<p>Шаг $i: a = $a, b = $b</p>";
            $i++;
        }
        echo "<p><strong>End of the loop:</strong> a = $a, b = $b</p>";
        ?>
    </div>

    <div class="cycle-block">
        <h3>Цикл do-while:</h3>
        <?php
        $a = 0;
        $b = 0;
        echo "<p><strong>Initial values:</strong> a = $a, b = $b</p>";
        $i = 0;
        do {
            $a += 10;
            $b += 5;
            echo "<p>Шаг $i: a = $a, b = $b</p>";
            $i++;
        } while ($i <= 5);
        echo "<p><strong>End of the loop:</strong> a = $a, b = $b</p>";
        ?>
    </div>
</div>

</body>
</html>
