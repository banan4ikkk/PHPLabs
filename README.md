# Лабораторная работа №1
## Дисциплина: PHP
### Тема: HTTP

**Выполнил:** студент группы IA2403, Демченко Юрий  
**Проверила:** V. Vișnevschi  
**Год:** 2026

---

## Цель работы
- Понять, что происходит, когда пользователь открывает сайт.
- Научиться находить и анализировать HTTP-запросы в браузере.
- Разобраться в назначении методов `GET`, `POST`, `PUT`, `DELETE`.

## Условия работы
- Анализ HTTP-запросов
- Составление HTTP-запросов

## Ход работы

### Задание 1. Анализ HTTP-запросов
Когда мы открываем страницу `https://en.wikipedia.org/wiki/HTTP` на сайте Wikipedia, браузер отправляет первый основной HTTP-запрос.

**URL запроса:** `https://en.wikipedia.org/wiki/HTTP`  
**Метод запроса:** `GET` — используется, потому что мы просто получаем страницу с сервера, а не отправляем данные.  
**Статус ответа:** `200 OK` — это значит, что запрос выполнен успешно и сервер вернул страницу.

#### Заголовки запроса (Request Headers)
- `User-Agent` — информация о браузере и системе пользователя
- `Accept` — какие типы данных может принять браузер
- `Host` — домен сайта
- `Accept-Language` — язык пользователя

#### Заголовки ответа (Response Headers)
- `Content-Type: text/html` — тип данных (HTML-страница)
- `Content-Length` — размер ответа
- `Date` — дата ответа сервера
- `Server` — тип сервера

При переходе по адресу `https://en.wikipedia.org/wiki/HTTPdsfdfs` я получил статус ответа `404 Not Found`, что означает, что страницы не существует на сервере.

### Задание 2. Анализ HTTP-запросов
После перехода на страницу `https://en.wikipedia.org/wiki/Special:Search` и поиска по слову `browser`, в Network можно увидеть следующие аспекты запроса.

**URL запроса:**  
`https://en.wikipedia.org/w/index.php?search=browser&title=Special%3ASearch`

**Метод запроса:** `GET`, потому что параметры передаются прямо в URL.

#### Query Parameters
- `search=browser` — что ищем
- `title=Special:Search` — страница поиска

### Задание 3. Анализ HTTP-запросов
При переходе на `google.com` происходит следующая цепочка:

- `http://google.com` → `301 redirect` — перенаправление с `http` на `https`
- `http://www.google.com` → `302 redirect` — временное перенаправление
- `https://www.google.com` → `200 OK` — успешная загрузка страницы

#### Заголовки запроса (Request Headers)
Примеры заголовков, которые отправляет браузер:
- `Host: www.google.com` — адрес сервера
- `User-Agent` — информация о браузере и системе
- `Accept` — какие форматы данных принимает клиент
- `Accept-Language` — язык пользователя

#### Заголовки ответа (Response Headers)
- `Content-Type: text/html` — тип данных (HTML-страница)
- `Cache-Control` — политика кэширования
- `Set-Cookie` — установка cookies
- `Strict-Transport-Security` — защита HTTPS
- `Server` — информация о сервере

### Задание 4. Составление HTTP-запросов

#### Пример GET-запроса
```http
GET / HTTP/1.1
Host: sandbox.usm.com
User-Agent: Iurii Demcenco
```

`User-Agent` — это строка, которая сообщает серверу, какой браузер, какая ОС и какое устройство использует клиент.

#### Пример POST-запроса
```http
POST /cars HTTP/1.1
Host: sandbox.usm.com

make=Toyota&model=Corolla&year=2020
```

`POST` используется для создания данных на сервере.

#### Пример PUT-запроса
```http
PUT /cars/1 HTTP/1.1
Host: sandbox.usm.com
User-Agent: Iurii Demcenco
Content-Type: application/json

{
  "make": "Toyota",
  "model": "Corolla",
  "year": 2021
}
```

`PATCH` частично обновляет ресурс, тогда как `PUT` обновляет его полностью.

#### Ещё один пример POST-запроса
```http
POST /cars HTTP/1.1
Host: sandbox.com
Content-Type: application/json
User-Agent: John Doe

model=Corolla&make=Toyota&year=2020
```

#### Возможный ответ сервера
```http
HTTP/1.1 201 Created
Content-Type: application/json

{
  "id": 1,
  "make": "Toyota",
  "model": "Corolla",
  "year": 2020
}
```

## Коды ответа HTTP
| Код | Когда возвращается |
|---|---|
| `200 OK` | Всё прошло успешно |
| `201 Created` | Ресурс успешно создан |
| `400 Bad Request` | Ошибка в данных |
| `401 Unauthorized` | Не авторизован |
| `403 Forbidden` | Нет прав доступа |
| `404 Not Found` | Ресурс не найден |
| `500 Internal Server Error` | Ошибка сервера |

## Вывод
В данной лабораторной работе я разобрался, как работает HTTP, научился анализировать запросы в браузере и узнал про методы `GET`, `POST` и `PUT`.
