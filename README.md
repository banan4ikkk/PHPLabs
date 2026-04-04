# Лабораторная работа №5
## Дисциплина: PHP
### Тема: Объектно-ориентированное программирование в PHP

**Выполнил:** студент группы IA2403, Демченко Юрий  
**Проверила:** V. Vișnevschi  
**Год:** 2026

---

## Цель работы
Освоить основы объектно-ориентированного программирования в PHP на практике. Научиться создавать собственные классы, использовать инкапсуляцию для защиты данных, разделять ответственность между классами, а также применять интерфейсы для построения гибкой архитектуры приложения. :contentReference[oaicite:2]{index=2}

## Условия работы
- Создание классов в PHP
- Использование строгой типизации
- Реализация инкапсуляции
- Разделение ответственности между классами
- Работа с интерфейсами
- Хранение, поиск, удаление и сортировка транзакций
- Вывод данных в HTML-таблицу
- Документирование методов с помощью PHPDoc :contentReference[oaicite:3]{index=3}

---

## Ход работы

### Задание 1. Включение строгой типизации

В начале файла `index.php` была включена строгая типизация:
<?php

declare(strict_types=1);

/**
 * Класс, описывающий одну банковскую транзакцию.
 */
class Transaction
{
    private int $id;
    private string $date;
    private float $amount;
    private string $description;
    private string $merchant;

    public function __construct(
        int $id,
        string $date,
        float $amount,
        string $description,
        string $merchant
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->amount = $amount;
        $this->description = $description;
        $this->merchant = $merchant;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getMerchant(): string
    {
        return $this->merchant;
    }

    public function getDaysSinceTransaction(): int
    {
        $transactionDate = new DateTime($this->date);
        $currentDate = new DateTime();
        return (int)$transactionDate->diff($currentDate)->format('%a');
    }

    public function getMerchantCategory(): string
    {
        return match (strtolower($this->merchant)) {
            'pyaterochka', 'magnit', 'lenta' => 'Супермаркет',
            'ozon', 'wildberries', 'aliexpress' => 'Интернет-магазин',
            'burger king', 'kfc', 'mcdonalds' => 'Фастфуд',
            'shell', 'gazprom' => 'АЗС',
            default => 'Другое',
        };
    }
}

interface TransactionStorageInterface
{
    public function addTransaction(Transaction $transaction): void;
    public function removeTransactionById(int $id): void;
    public function getAllTransactions(): array;
    public function findById(int $id): ?Transaction;
}

class TransactionRepository implements TransactionStorageInterface
{
    private array $transactions = [];

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    public function removeTransactionById(int $id): void
    {
        foreach ($this->transactions as $key => $transaction) {
            if ($transaction->getId() === $id) {
                unset($this->transactions[$key]);
            }
        }

        $this->transactions = array_values($this->transactions);
    }

    public function getAllTransactions(): array
    {
        return $this->transactions;
    }

    public function findById(int $id): ?Transaction
    {
        foreach ($this->transactions as $transaction) {
            if ($transaction->getId() === $id) {
                return $transaction;
            }
        }

        return null;
    }
}

class TransactionManager
{
    public function __construct(
        private TransactionStorageInterface $repository
    ) {
    }

    public function calculateTotalAmount(): float
    {
        $total = 0.0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $total += $transaction->getAmount();
        }

        return $total;
    }

    public function calculateTotalAmountByDateRange(string $startDate, string $endDate): float
    {
        $total = 0.0;
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $transactionDate = new DateTime($transaction->getDate());

            if ($transactionDate >= $start && $transactionDate <= $end) {
                $total += $transaction->getAmount();
            }
        }

        return $total;
    }

    public function countTransactionsByMerchant(string $merchant): int
    {
        $count = 0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            if (mb_strtolower($transaction->getMerchant()) === mb_strtolower($merchant)) {
                $count++;
            }
        }

        return $count;
    }

    public function sortTransactionsByDate(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function (Transaction $a, Transaction $b) {
            return strtotime($a->getDate()) <=> strtotime($b->getDate());
        });

        return $transactions;
    }

    public function sortTransactionsByAmountDesc(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, function (Transaction $a, Transaction $b) {
            return $b->getAmount() <=> $a->getAmount();
        });

        return $transactions;
    }
}

final class TransactionTableRenderer
{
    public function render(array $transactions): string
    {
        $html = '<table border="1" cellpadding="8" cellspacing="0">';
        $html .= '<tr>';
        $html .= '<th>ID транзакции</th>';
        $html .= '<th>Дата</th>';
        $html .= '<th>Сумма</th>';
        $html .= '<th>Описание</th>';
        $html .= '<th>Получатель</th>';
        $html .= '<th>Категория получателя</th>';
        $html .= '<th>Дней с момента транзакции</th>';
        $html .= '</tr>';

        foreach ($transactions as $transaction) {
            $html .= '<tr>';
            $html .= '<td>' . $transaction->getId() . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getDate()) . '</td>';
            $html .= '<td>' . $transaction->getAmount() . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getDescription()) . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getMerchant()) . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getMerchantCategory()) . '</td>';
            $html .= '<td>' . $transaction->getDaysSinceTransaction() . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }
}

$repository = new TransactionRepository();

$transactions = [
    new Transaction(1, '2024-09-01', 1200.50, 'Покупка продуктов', 'Pyaterochka'),
    new Transaction(2, '2024-09-03', 5400.00, 'Покупка на маркетплейсе', 'Ozon'),
    new Transaction(3, '2024-09-05', 850.75, 'Обед', 'KFC'),
    new Transaction(4, '2024-09-07', 3000.00, 'Заправка автомобиля', 'Shell'),
    new Transaction(5, '2024-09-09', 15000.00, 'Покупка техники', 'Wildberries'),
    new Transaction(6, '2024-09-11', 430.20, 'Продукты', 'Magnit'),
    new Transaction(7, '2024-09-13', 999.99, 'Ужин', 'Burger King'),
    new Transaction(8, '2024-09-15', 2700.00, 'Бытовые товары', 'Lenta'),
    new Transaction(9, '2024-09-17', 6200.00, 'Одежда', 'AliExpress'),
    new Transaction(10, '2024-09-19', 1800.00, 'Топливо', 'Gazprom'),
];

foreach ($transactions as $transaction) {
    $repository->addTransaction($transaction);
}

$manager = new TransactionManager($repository);
$renderer = new TransactionTableRenderer();

$totalAmount = $manager->calculateTotalAmount();
$dateRangeAmount = $manager->calculateTotalAmountByDateRange('2024-09-01', '2024-09-10');
$merchantCount = $manager->countTransactionsByMerchant('Ozon');
$sortedTransactions = $manager->sortTransactionsByAmountDesc();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Банковские транзакции</title>
</head>
<body>
    <h1>Список банковских транзакций</h1>

    <p><strong>Общая сумма всех транзакций:</strong> <?= $totalAmount ?></p>
    <p><strong>Сумма транзакций за период 2024-09-01 - 2024-09-10:</strong> <?= $dateRangeAmount ?></p>
    <p><strong>Количество транзакций у получателя Ozon:</strong> <?= $merchantCount ?></p>

    <h2>Транзакции, отсортированные по сумме (по убыванию)</h2>
    <?= $renderer->render($sortedTransactions); ?>
</body>
</html>

        return $html;
    }
}
