<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css"/>
    <title>График работы сотрудников</title>
</head>
    <body>
        <?php
            $currentDay = date('N');

            function workSchedule($currentDay, $employee){
                if ($employee == 'John Styles'){
                    if (in_array($currentDay, [1, 3, 5])){
                        return '8:00 - 12:00';
                    }
                    else{
                        return 'Нерабочий день';
                    }
                }

                if ($employee == 'Jane Doe'){
                    if (in_array($currentDay, [2, 4, 6])){
                        return '12:00 - 16:00';
                    }
                    else{
                        return 'Нерабочий день';
                    }
                }
                if ($currentDay == 7) {
                    return 'Нерабочий день';
                }
            }

            $employees = [
                ['id' => 1, 'name' => 'John Styles'],
                ['id' => 2, 'name' => 'Jane Doe']
            ];
        ?>
        <h1>График работы</h1>
        <hr>
        <h2>График работы сотрудников</h2>
        <table>
            <tr>
                <th>№</th>
                <th>Фамилия Имя</th>
                <th>График работы</th>
            </tr>
            <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?= $employee['id'] ?></td>
                    <td><?= $employee['name'] ?></td>
                    <td><?= workSchedule($currentDay, $employee['name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </body>
</html>
