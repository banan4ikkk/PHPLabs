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

```php
<?php

declare(strict_types=1);

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

    /**
     * @param int $id Уникальный идентификатор транзакции
     * @param string $date Дата транзакции
     * @param float $amount Сумма транзакции
     * @param string $description Описание платежа
     * @param string $merchant Получатель платежа
     */
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

/**
 * Репозиторий для хранения транзакций.
 */
class TransactionRepository implements TransactionStorageInterface
{
    /**
     * @var Transaction[]
     */
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
}/**
 * Класс для вывода транзакций в виде HTML-таблицы.
 */
final class TransactionTableRenderer
{
    /**
     * @param Transaction[] $transactions
     * @return string
     */
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
