# Лабораторная работа №7
## Дисциплина: PHP

### Тема: Шаблонизация

Выполнил: студент группы IA2403, Демченко Юрий  
Проверила: V. Vișnevschi  
Год: 2026

* * *

## Цель работы

Освоить принципы шаблонизации в PHP.

В рамках лабораторной работы необходимо научиться:

* разделять логику приложения и представление;
* использовать нативные PHP-шаблоны;
* подключать готовый шаблонизатор Twig;
* применять наследование шаблонов;
* использовать блоки в шаблонах;
* улучшать структуру PHP-проекта.

---

## Условия работы

В данной лабораторной работе необходимо продолжить разработку проекта из лабораторной работы №6.

Проект из лабораторной работы №6 назывался:

## Дневник настроения

В лабораторной работе №6 была создана форма, которая позволяла пользователю добавлять записи о своем настроении.

В лабораторной работе №7 проект был улучшен.  
Код был разделен на два уровня:

* логика;
* представление.

Логика отвечает за обработку данных, чтение и запись файла, валидацию и подготовку переменных.

Представление отвечает только за отображение данных пользователю.

Также в проект были добавлены два варианта шаблонизации:

* нативные PHP-шаблоны;
* шаблоны Twig.

---

## Шаг 1. Рефакторинг проекта

Сначала проект был разделен на несколько папок и файлов.

Пример структуры проекта:

```text
project/
├── templates/
│   ├── layout.php
│   ├── form.php
│   └── list.php
├── templates_twig/
│   ├── layout.twig
│   ├── form.twig
│   ├── list.twig
│   └── page.twig
├── src/
│   ├── functions.php
│   └── handler.php
├── vendor/
├── index.php
├── twig_index.php
├── composer.json
└── data.json
```

Описание основных файлов:

| Файл | Назначение |
|---|---|
| `index.php` | Главная точка входа для варианта с нативными PHP-шаблонами |
| `twig_index.php` | Главная точка входа для варианта с Twig |
| `data.json` | Файл для хранения записей дневника настроения |
| `src/functions.php` | Функции для чтения, записи и сортировки данных |
| `src/handler.php` | Обработка данных, отправленных из формы |
| `templates/layout.php` | Общий макет страницы |
| `templates/form.php` | Шаблон формы |
| `templates/list.php` | Шаблон таблицы записей |
| `templates_twig/layout.twig` | Общий Twig-макет |
| `templates_twig/form.twig` | Twig-шаблон формы |
| `templates_twig/list.twig` | Twig-шаблон таблицы |
| `templates_twig/page.twig` | Страница, которая наследует общий Twig-макет |

Такое разделение делает проект более аккуратным.  
Теперь код обработки данных не смешивается с HTML-разметкой.

---

## Шаг 2. Нативные PHP-шаблоны

На первом этапе был реализован собственный механизм шаблонизации с помощью обычных PHP-файлов.

Идея заключается в том, что основной файл `index.php` подготавливает данные, а затем подключает шаблон.

---

## Файл `src/functions.php`

В файле `functions.php` находятся функции для работы с данными.

#### Пример кода

```php
<?php

declare(strict_types=1);

/**
 * Возвращает список всех записей из JSON-файла.
 *
 * @param string $filePath Путь к файлу с данными.
 * @return array Список записей дневника настроения.
 */
function getMoodEntries(string $filePath): array
{
    if (!file_exists($filePath)) {
        return [];
    }

    $jsonData = file_get_contents($filePath);

    return json_decode($jsonData, true) ?? [];
}

/**
 * Сохраняет новую запись в JSON-файл.
 *
 * @param string $filePath Путь к файлу с данными.
 * @param array $entry Новая запись дневника.
 * @return void
 */
function saveMoodEntry(string $filePath, array $entry): void
{
    $entries = getMoodEntries($filePath);

    $entries[] = $entry;

    file_put_contents(
        $filePath,
        json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

/**
 * Сортирует записи по выбранному полю.
 *
 * @param array $entries Массив записей.
 * @param string $sort Поле для сортировки.
 * @return array Отсортированный массив записей.
 */
function sortMoodEntries(array $entries, string $sort): array
{
    $allowedSortFields = ['name', 'entry_date', 'mood', 'energy_level', 'created_at'];

    if (!in_array($sort, $allowedSortFields, true)) {
        $sort = 'entry_date';
    }

    usort($entries, function (array $a, array $b) use ($sort): int {
        return $a[$sort] <=> $b[$sort];
    });

    return $entries;
}
```

В этом файле нет HTML-кода.  
Он отвечает только за работу с данными.

---

## Файл `src/handler.php`

Файл `handler.php` обрабатывает данные, которые пользователь отправляет из формы.

#### Пример кода

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $entryDate = trim($_POST['entry_date'] ?? '');
    $mood = trim($_POST['mood'] ?? '');
    $energyLevel = trim($_POST['energy_level'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    $allowedMoods = ['happy', 'sad', 'normal', 'angry', 'tired'];

    if ($name === '') {
        $errors[] = 'Имя обязательно для заполнения.';
    }

    if ($entryDate === '') {
        $errors[] = 'Дата записи обязательна.';
    }

    if (!in_array($mood, $allowedMoods, true)) {
        $errors[] = 'Выбрано некорректное настроение.';
    }

    if (!is_numeric($energyLevel) || (int)$energyLevel < 1 || (int)$energyLevel > 10) {
        $errors[] = 'Уровень энергии должен быть числом от 1 до 10.';
    }

    if (strlen($description) < 10) {
        $errors[] = 'Описание дня должно содержать минимум 10 символов.';
    }

    if ($reason === '') {
        $errors[] = 'Причина настроения обязательна.';
    }

    if (empty($errors)) {
        $entry = [
            'name' => $name,
            'entry_date' => $entryDate,
            'mood' => $mood,
            'energy_level' => (int)$energyLevel,
            'description' => $description,
            'reason' => $reason,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        saveMoodEntry(__DIR__ . '/../data.json', $entry);

        header('Location: ../index.php');
        exit;
    }
}
```

Этот файл занимается только логикой обработки формы.

Он:

* проверяет метод запроса;
* получает данные из `$_POST`;
* валидирует данные;
* сохраняет запись в файл;
* перенаправляет пользователя обратно на главную страницу.

---

## Файл `index.php`

Файл `index.php` является точкой входа в приложение.

Он подключает необходимые файлы, получает данные и передает их в шаблон.

#### Пример кода

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/handler.php';

$filePath = __DIR__ . '/data.json';

$sort = $_GET['sort'] ?? 'entry_date';

$entries = getMoodEntries($filePath);
$entries = sortMoodEntries($entries, $sort);

$title = 'Дневник настроения';

require __DIR__ . '/templates/layout.php';
```

В этом файле нет большой HTML-разметки.  
Он только подготавливает переменные:

* `$filePath`;
* `$sort`;
* `$entries`;
* `$title`.

После этого подключается общий шаблон `layout.php`.

---

## Файл `templates/layout.php`

Файл `layout.php` содержит общий HTML-макет страницы.

#### Пример кода

```php
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($title) ?></h1>
    </header>

    <main>
        <?php require __DIR__ . '/form.php'; ?>
        <?php require __DIR__ . '/list.php'; ?>
    </main>

    <footer>
        <p>Лабораторная работа №7 — Шаблонизация</p>
    </footer>
</body>
</html>
```

В этом шаблоне находятся общие части страницы:

* `head`;
* `header`;
* `main`;
* `footer`.

Форма и таблица подключаются как отдельные шаблоны.

---

## Файл `templates/form.php`

Файл `form.php` содержит только HTML-форму.

#### Пример кода

```php
<section>
    <h2>Добавить запись</h2>

    <form action="index.php" method="POST">
        <label for="name">Имя:</label>
        <input 
            type="text" 
            id="name" 
            name="name" 
            required 
            minlength="2" 
            maxlength="50"
        >

        <label for="entry_date">Дата записи:</label>
        <input 
            type="date" 
            id="entry_date" 
            name="entry_date" 
            required
        >

        <label for="mood">Настроение:</label>
        <select id="mood" name="mood" required>
            <option value="">Выберите настроение</option>
            <option value="happy">Хорошее</option>
            <option value="sad">Плохое</option>
            <option value="normal">Обычное</option>
            <option value="angry">Злое</option>
            <option value="tired">Уставшее</option>
        </select>

        <label for="energy_level">Уровень энергии:</label>
        <input 
            type="number" 
            id="energy_level" 
            name="energy_level" 
            min="1" 
            max="10" 
            required
        >

        <label for="description">Описание дня:</label>
        <textarea 
            id="description" 
            name="description" 
            required 
            minlength="10" 
            maxlength="1000"
        ></textarea>

        <label for="reason">Причина настроения:</label>
        <input 
            type="text" 
            id="reason" 
            name="reason" 
            required 
            minlength="3" 
            maxlength="100"
        >

        <button type="submit">Сохранить запись</button>
    </form>
</section>
```

Этот файл не выполняет обработку данных.  
Он только отображает форму пользователю.

---

## Файл `templates/list.php`

Файл `list.php` отвечает за вывод записей в виде HTML-таблицы.

#### Пример кода

```php
<section>
    <h2>Список записей</h2>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th><a href="?sort=name">Имя</a></th>
                <th><a href="?sort=entry_date">Дата записи</a></th>
                <th><a href="?sort=mood">Настроение</a></th>
                <th><a href="?sort=energy_level">Энергия</a></th>
                <th>Описание дня</th>
                <th>Причина</th>
                <th><a href="?sort=created_at">Дата создания</a></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($entries as $entry): ?>
                <tr>
                    <td><?= htmlspecialchars($entry['name']) ?></td>
                    <td><?= htmlspecialchars($entry['entry_date']) ?></td>
                    <td><?= htmlspecialchars($entry['mood']) ?></td>
                    <td><?= htmlspecialchars((string)$entry['energy_level']) ?></td>
                    <td><?= htmlspecialchars($entry['description']) ?></td>
                    <td><?= htmlspecialchars($entry['reason']) ?></td>
                    <td><?= htmlspecialchars($entry['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
```

В этом шаблоне данные только выводятся на страницу.

Для защиты от XSS используется функция `htmlspecialchars()`.

---

## Шаг 3. Подключение Twig

На втором этапе был подключен готовый шаблонизатор Twig.

Для установки Twig использовался Composer.

#### Команда установки

```bash
composer require twig/twig
```

После установки в проекте появилась папка `vendor`, а также файлы `composer.json` и `composer.lock`.

Twig был подключен через автозагрузчик Composer:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

---

## Файл `twig_index.php`

Файл `twig_index.php` является точкой входа для версии проекта с Twig.

#### Пример кода

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/handler.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$filePath = __DIR__ . '/data.json';

$sort = $_GET['sort'] ?? 'entry_date';

$entries = getMoodEntries($filePath);
$entries = sortMoodEntries($entries, $sort);

$loader = new FilesystemLoader(__DIR__ . '/templates_twig');
$twig = new Environment($loader);

echo $twig->render('page.twig', [
    'title' => 'Дневник настроения',
    'entries' => $entries,
]);
```

Этот файл выполняет следующие действия:

* подключает Composer;
* подключает функции проекта;
* получает список записей;
* сортирует записи;
* настраивает Twig;
* передает данные в Twig-шаблон.

---

## Шаг 4. Twig-шаблоны

Для Twig были созданы отдельные шаблоны:

```text
templates_twig/
├── layout.twig
├── form.twig
├── list.twig
└── page.twig
```

Twig-шаблоны отличаются от обычных PHP-шаблонов тем, что в них используется специальный синтаксис.

---

## Файл `templates_twig/layout.twig`

Файл `layout.twig` является основным шаблоном страницы.

#### Пример кода

```twig
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>{{ title }}</h1>
    </header>

    <main>
        {% block content %}{% endblock %}
    </main>

    <footer>
        <p>Лабораторная работа №7 — Шаблонизация</p>
    </footer>
</body>
</html>
```

В этом шаблоне используется блок:

```twig
{% block content %}{% endblock %}
```

В этот блок другие шаблоны могут вставлять свой контент.

---

## Файл `templates_twig/form.twig`

Файл `form.twig` содержит форму добавления записи.

#### Пример кода

```twig
<section>
    <h2>Добавить запись</h2>

    <form action="twig_index.php" method="POST">
        <label for="name">Имя:</label>
        <input 
            type="text" 
            id="name" 
            name="name" 
            required 
            minlength="2" 
            maxlength="50"
        >

        <label for="entry_date">Дата записи:</label>
        <input 
            type="date" 
            id="entry_date" 
            name="entry_date" 
            required
        >

        <label for="mood">Настроение:</label>
        <select id="mood" name="mood" required>
            <option value="">Выберите настроение</option>
            <option value="happy">Хорошее</option>
            <option value="sad">Плохое</option>
            <option value="normal">Обычное</option>
            <option value="angry">Злое</option>
            <option value="tired">Уставшее</option>
        </select>

        <label for="energy_level">Уровень энергии:</label>
        <input 
            type="number" 
            id="energy_level" 
            name="energy_level" 
            min="1" 
            max="10" 
            required
        >

        <label for="description">Описание дня:</label>
        <textarea 
            id="description" 
            name="description" 
            required 
            minlength="10" 
            maxlength="1000"
        ></textarea>

        <label for="reason">Причина настроения:</label>
        <input 
            type="text" 
            id="reason" 
            name="reason" 
            required 
            minlength="3" 
            maxlength="100"
        >

        <button type="submit">Сохранить запись</button>
    </form>
</section>
```

Этот шаблон отвечает только за отображение формы.

---

## Файл `templates_twig/list.twig`

Файл `list.twig` отвечает за вывод списка записей.

#### Пример кода

```twig
<section>
    <h2>Список записей</h2>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th><a href="?sort=name">Имя</a></th>
                <th><a href="?sort=entry_date">Дата записи</a></th>
                <th><a href="?sort=mood">Настроение</a></th>
                <th><a href="?sort=energy_level">Энергия</a></th>
                <th>Описание дня</th>
                <th>Причина</th>
                <th><a href="?sort=created_at">Дата создания</a></th>
            </tr>
        </thead>

        <tbody>
            {% for entry in entries %}
                <tr>
                    <td>{{ entry.name }}</td>
                    <td>{{ entry.entry_date }}</td>
                    <td>{{ entry.mood }}</td>
                    <td>{{ entry.energy_level }}</td>
                    <td>{{ entry.description }}</td>
                    <td>{{ entry.reason }}</td>
                    <td>{{ entry.created_at }}</td>
                </tr>
            {% else %}
                <tr>
                    <td colspan="7">Записей пока нет.</td>
                </tr>
            {% endfor %}
        </tbody>
    </table>
</section>
```

В этом шаблоне используется цикл Twig:

```twig
{% for entry in entries %}
```

Он перебирает все записи и выводит их в таблицу.

Также используется блок:

```twig
{% else %}
```

Он выводит сообщение, если записей пока нет.

---

## Файл `templates_twig/page.twig`

Файл `page.twig` показывает использование наследования шаблонов.

#### Пример кода

```twig
{% extends "layout.twig" %}

{% block content %}
    {% include "form.twig" %}
    {% include "list.twig" %}
{% endblock %}
```

С помощью строки:

```twig
{% extends "layout.twig" %}
```

шаблон `page.twig` наследует структуру общего шаблона.

С помощью блока:

```twig
{% block content %}
```

он вставляет форму и таблицу в основную часть страницы.

Также используются подключения шаблонов:

```twig
{% include "form.twig" %}
{% include "list.twig" %}
```

Такой подход позволяет не повторять общий HTML-код в каждом шаблоне.

---

## Сравнение двух подходов

В проекте были реализованы оба варианта шаблонизации:

| Подход | Описание |
|---|---|
| Нативные PHP-шаблоны | Используются обычные `.php`-файлы, в которых HTML смешан с небольшими PHP-вставками |
| Twig | Используются специальные `.twig`-шаблоны с отдельным синтаксисом |

Нативные PHP-шаблоны проще подключить, потому что не нужны сторонние библиотеки.

Twig удобнее для больших проектов, потому что он поддерживает наследование, блоки, include и более чистый синтаксис.

---

## Документирование кода

Код был задокументирован с помощью PHPDoc.

PHPDoc-комментарии были добавлены к функциям, которые отвечают за чтение, сохранение и сортировку данных.

#### Пример PHPDoc

```php
/**
 * Сортирует записи дневника настроения по выбранному полю.
 *
 * @param array $entries Список записей.
 * @param string $sort Поле для сортировки.
 * @return array Отсортированный список записей.
 */
function sortMoodEntries(array $entries, string $sort): array
{
    // Код функции
}
```

PHPDoc помогает быстрее понять назначение функции, ее параметры и возвращаемое значение.

---

## Результат работы

В результате выполнения лабораторной работы проект **«Дневник настроения»** был переработан.

Теперь проект имеет более правильную структуру:

* логика вынесена в папку `src`;
* представление вынесено в папки `templates` и `templates_twig`;
* данные хранятся в файле `data.json`;
* нативные PHP-шаблоны используются отдельно;
* Twig-шаблоны используются отдельно;
* реализовано наследование шаблонов Twig.

Приложение позволяет:

* добавлять записи дневника настроения;
* валидировать данные;
* сохранять записи в JSON-файл;
* читать данные из файла;
* выводить записи в таблицу;
* сортировать записи по разным полям.

---

# Контрольные вопросы

## 1. В чём отличие нативных PHP-шаблонов от шаблонизатора Twig? Какие преимущества и недостатки у каждого подхода?

Нативные PHP-шаблоны — это обычные `.php`-файлы, в которых HTML-код содержит небольшие PHP-вставки.

Например:

```php
<h1><?= htmlspecialchars($title) ?></h1>
```

Преимущества нативных PHP-шаблонов:

* не нужно устанавливать дополнительные библиотеки;
* работают сразу в PHP;
* легко понять на начальном уровне;
* можно использовать обычный PHP-код.

Недостатки:

* легко смешать логику и HTML;
* код может стать неаккуратным;
* нужно вручную использовать `htmlspecialchars()`;
* в больших проектах сложнее поддерживать шаблоны.

Twig — это готовый шаблонизатор для PHP.

В Twig используется отдельный синтаксис:

```twig
<h1>{{ title }}</h1>
```

Преимущества Twig:

* более чистые шаблоны;
* поддержка наследования шаблонов;
* поддержка блоков;
* поддержка подключения других шаблонов через `include`;
* удобен для больших проектов.

Недостатки Twig:

* нужно устанавливать через Composer;
* нужно изучить новый синтаксис;
* проект становится зависимым от сторонней библиотеки.

В данной лабораторной работе были реализованы оба варианта, чтобы сравнить их между собой.

---

## 2. Зачем разделять логику и представление в проекте? Какие проблемы могут возникнуть, если смешивать их в одном файле?

Логику и представление нужно разделять для того, чтобы код был более понятным и удобным для поддержки.

Логика отвечает за:

* обработку запросов;
* получение данных;
* валидацию;
* сохранение данных;
* сортировку.

Представление отвечает за:

* HTML-разметку;
* вывод данных;
* внешний вид страницы.

Если смешивать логику и представление в одном файле, могут возникнуть проблемы:

* файл становится слишком большим;
* код сложнее читать;
* труднее искать ошибки;
* сложнее менять дизайн;
* сложнее повторно использовать части страницы;
* можно случайно сломать обработку данных при изменении HTML;
* проект становится труднее расширять.

В данной лабораторной работе логика была вынесена в папку `src`, а шаблоны — в отдельные папки `templates` и `templates_twig`.

---

## 3. Что такое наследование шаблонов в Twig? Как работают `{% extends %}` и `{% block %}`?

Наследование шаблонов в Twig позволяет создать один общий шаблон страницы и использовать его для разных страниц проекта.

Например, общий шаблон `layout.twig` может содержать:

* `DOCTYPE`;
* тег `html`;
* `head`;
* `header`;
* `main`;
* `footer`.

А другие шаблоны могут наследовать этот макет и вставлять свой контент в нужные места.

Команда:

```twig
{% extends "layout.twig" %}
```

означает, что текущий шаблон наследует структуру шаблона `layout.twig`.

Команда:

```twig
{% block content %}
    ...
{% endblock %}
```

создает блок, в который можно вставить уникальное содержимое страницы.

Пример:

```twig
{% extends "layout.twig" %}

{% block content %}
    {% include "form.twig" %}
    {% include "list.twig" %}
{% endblock %}
```

В этом примере шаблон наследует общий макет и вставляет в блок `content` форму и таблицу.

Главное преимущество наследования заключается в том, что общий HTML-код не нужно повторять в каждом файле.

---

## Вывод

В данной лабораторной работе я изучил принципы шаблонизации в PHP.

Я продолжил разработку проекта **«Дневник настроения»** из лабораторной работы №6 и разделил его на логику и представление.

В работе были реализованы два подхода:

* нативные PHP-шаблоны;
* шаблонизатор Twig.

Для нативных шаблонов были созданы отдельные PHP-файлы:

* `layout.php`;
* `form.php`;
* `list.php`.

Для Twig были созданы отдельные `.twig`-шаблоны:

* `layout.twig`;
* `form.twig`;
* `list.twig`;
* `page.twig`.

В результате код проекта стал более структурированным, читаемым и удобным для дальнейшего развития.
