# Лабораторная работа №4
## Дисциплина: PHP
### Тема: Массивы и функции

**Выполнил:** студент группы IA2403, Демченко Юрий  
**Проверила:** V. Vișnevschi  
**Год:** 2026

---

## Цель работы
Освоить работу с массивами в PHP, применяя различные операции: создание, добавление, удаление, сортировка и поиск. Закрепить навыки работы с функциями, включая передачу аргументов, возвращаемые значения и анонимные функции.

## Условия работы
- Создание и обработка массивов в PHP
- Использование ассоциативных массивов
- Реализация пользовательских функций
- Сортировка и поиск данных
- Работа с файловой системой
- Вывод изображений из папки в виде галереи
- Документирование функций с помощью PHPDoc

## Ход работы

### Задание 1. Работа с массивами

#### Подготовка среды
Для выполнения лабораторной работы был создан файл `index.php`, в начале которого была включена строгая типизация:

```php
<?php

declare(strict_types=1);
```

#### Создание массива транзакций
Был создан массив `$transactions`, содержащий информацию о банковских транзакциях. Каждая запись хранит идентификатор, дату, сумму, описание и название получателя платежа.

```php
<?php

declare(strict_types=1);

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
    [
        "id" => 3,
        "date" => "2021-03-10",
        "amount" => 220.30,
        "description" => "Online shopping",
        "merchant" => "Amazon",
    ],
];
```

#### Вывод списка транзакций
Для отображения списка транзакций использовался цикл `foreach`, который выводит данные в HTML-таблицу.

```php
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Дата</th>
            <th>Сумма</th>
            <th>Описание</th>
            <th>Получатель</th>
            <th>Дней с момента транзакции</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?= $transaction['id'] ?></td>
                <td><?= $transaction['date'] ?></td>
                <td><?= $transaction['amount'] ?></td>
                <td><?= $transaction['description'] ?></td>
                <td><?= $transaction['merchant'] ?></td>
                <td><?= daysSinceTransaction($transaction['date']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

#### Реализация функций
В рамках задания были реализованы функции для подсчёта общей суммы, поиска транзакций, определения количества дней с даты операции и добавления новой транзакции.

```php
<?php

/**
 * Вычисляет общую сумму всех транзакций.
 *
 * @param array $transactions Массив транзакций.
 * @return float Общая сумма транзакций.
 */
function calculateTotalAmount(array $transactions): float
{
    $total = 0;
    foreach ($transactions as $transaction) {
        $total += $transaction['amount'];
    }
    return $total;
}

/**
 * Ищет транзакции по части описания.
 *
 * @param array $transactions Массив транзакций.
 * @param string $descriptionPart Часть описания.
 * @return array Найденные транзакции.
 */
function findTransactionByDescription(array $transactions, string $descriptionPart): array
{
    $result = [];
    foreach ($transactions as $transaction) {
        if (stripos($transaction['description'], $descriptionPart) !== false) {
            $result[] = $transaction;
        }
    }
    return $result;
}

/**
 * Ищет транзакцию по идентификатору с помощью foreach.
 *
 * @param array $transactions Массив транзакций.
 * @param int $id Идентификатор транзакции.
 * @return array|null Найденная транзакция или null.
 */
function findTransactionById(array $transactions, int $id): ?array
{
    foreach ($transactions as $transaction) {
        if ($transaction['id'] === $id) {
            return $transaction;
        }
    }
    return null;
}

/**
 * Ищет транзакцию по идентификатору с помощью array_filter.
 *
 * @param array $transactions Массив транзакций.
 * @param int $id Идентификатор транзакции.
 * @return array|null Найденная транзакция или null.
 */
function findTransactionByIdWithFilter(array $transactions, int $id): ?array
{
    $filtered = array_filter($transactions, fn($transaction) => $transaction['id'] === $id);
    return !empty($filtered) ? array_values($filtered)[0] : null;
}

/**
 * Возвращает количество дней с момента транзакции.
 *
 * @param string $date Дата транзакции.
 * @return int Количество дней.
 */
function daysSinceTransaction(string $date): int
{
    $transactionDate = new DateTime($date);
    $currentDate = new DateTime();
    return (int)$transactionDate->diff($currentDate)->format('%a');
}

/**
 * Добавляет новую транзакцию в массив.
 *
 * @param int $id Идентификатор транзакции.
 * @param string $date Дата транзакции.
 * @param float $amount Сумма.
 * @param string $description Описание.
 * @param string $merchant Получатель платежа.
 * @return void
 */
function addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void
{
    global $transactions;

    $transactions[] = [
        "id" => $id,
        "date" => $date,
        "amount" => $amount,
        "description" => $description,
        "merchant" => $merchant,
    ];
}
```
#### Результат
![О](Screenshot_1.png)
#### Сортировка транзакций
Для сортировки транзакций использовалась функция `usort()`.

Сортировка по дате:

```php
usort($transactions, function ($a, $b) {
    return strtotime($a['date']) <=> strtotime($b['date']);
});
```

Сортировка по сумме по убыванию:

```php
usort($transactions, function ($a, $b) {
    return $b['amount'] <=> $a['amount'];
});
```

#### Вывод общей суммы
В конце таблицы была выведена общая сумма всех транзакций:

```php
<p><strong>Общая сумма транзакций:</strong> <?= calculateTotalAmount($transactions) ?></p>
```
#### Результат
![О](Screenshot_2.png)

---

### Задание 2. Работа с файловой системой

Для выполнения второй части лабораторной работы была создана директория `image`, в которую были помещены изображения формата `.jpg`. Затем был создан PHP-скрипт для чтения содержимого папки и вывода изображений на страницу в виде галереи.

#### Пример кода

```php
<?php
$dir = 'image/';
$files = scandir($dir);

if ($files === false) {
    return;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Галерея изображений</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        header, footer {
            text-align: center;
            padding: 15px;
            background-color: #f2f2f2;
            margin-bottom: 20px;
        }

        nav {
            margin-bottom: 20px;
            text-align: center;
        }

        nav a {
            margin: 0 10px;
            text-decoration: none;
            color: black;
            font-weight: bold;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            max-width: 900px;
            margin: 0 auto;
        }

        .gallery img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<header>
    <h1>Cat Gallery</h1>
</header>

<nav>
    <a href="#">About Cats</a>
    <a href="#">News</a>
    <a href="#">Contacts</a>
</nav>

<main>
    <h2 style="text-align:center;">#cats</h2>
    <p style="text-align:center;">Explore a world of cats</p>

    <div class="gallery">
        <?php
        for ($i = 0; $i < count($files); $i++) {
            if ($files[$i] !== "." && $files[$i] !== "..") {
                $path = $dir . $files[$i];
                echo "<img src='$path' alt='cat image'>";
            }
        }
        ?>
    </div>
</main>

<footer>
    <p>USM © 2024</p>
</footer>

</body>
</html>
```

#### Результат
В результате была получена веб-страница с хедером, навигационным меню, контентной частью и футером. Все изображения из папки `image` были автоматически считаны и выведены на экран в виде аккуратной галереи.
#### Результат
![О](Screenshot_3.png)

---

## Контрольные вопросы

### 1. Что такое массивы в PHP?
Массив в PHP — это структура данных, которая позволяет хранить несколько значений в одной переменной. Массивы могут быть индексированными, ассоциативными и многомерными.

### 2. Каким образом можно создать массив в PHP?
Массив можно создать с помощью функции `array()` или сокращённого синтаксиса `[]`.

Примеры:

```php
$numbers = array(1, 2, 3);
$colors = ['red', 'green', 'blue'];
```

### 3. Для чего используется цикл `foreach`?
Цикл `foreach` используется для перебора элементов массива. Он особенно удобен, когда нужно последовательно обработать все значения массива без обращения к индексам вручную.

Пример:

```php
foreach ($colors as $color) {
    echo $color . "<br>";
}
```

## Вывод
В данной лабораторной работе я изучил работу с массивами и функциями в PHP. Я научился создавать и обрабатывать массивы транзакций, выполнять сортировку, поиск и добавление новых элементов, а также применять пользовательские функции с типизацией и документацией PHPDoc. Кроме этого, я реализовал вывод изображений из папки `image` в виде веб-галереи, что позволило закрепить навыки работы с файловой системой и генерацией HTML с помощью PHP.
