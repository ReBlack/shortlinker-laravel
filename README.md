# Shortlinker

Сервис сокращения ссылок с генерацией QR-кодов. Построен на Laravel 12, работает в Docker-окружении (PHP 8.3 FPM + Nginx + MariaDB).

## Возможности

- Сокращение URL до 8-символьного хеша
- Генерация QR-кодов для коротких ссылок (PNG)
- Подсчёт переходов по каждой ссылке
- Валидация URL (проверка доступности через CURL, DNS, фильтрация приватных IP)
- Дедупликация — повторное сокращение той же ссылки возвращает существующий хеш
- REST API для интеграции

## Стек технологий

| Компонент      | Технология                            |
|----------------|---------------------------------------|
| Backend        | PHP 8.3, Laravel 12                   |
| База данных    | MariaDB 11.4                          |
| Веб-сервер     | Nginx (Alpine)                        |
| Контейнеры     | Docker, Docker Compose                |
| QR-коды        | chillerlan/php-qrcode 5.0             |
| Frontend       | Bootstrap 5.3, jQuery, Tailwind CSS 4 |
| Сборка         | Vite 7                                |
| Тесты          | PHPUnit 11, Mockery                   |
| Качество кода  | GrumPHP, PHP-CS-Fixer, Laravel Pint   |

## Структура проекта

```
shortlinker_laravel/
├── docker/                     # Docker-окружение
│   ├── php/
│   │   ├── Dockerfile          # PHP 8.3 FPM с расширениями
│   │   └── php.ini             # Конфигурация PHP
│   ├── nginx/
│   │   └── nginx.conf.template # Конфигурация Nginx
│   ├── docker-compose.yaml     # Оркестрация контейнеров
│   └── .env                    # Переменные окружения Docker
└── site/                       # Laravel-приложение
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/    # UrlController
    │   │   └── Requests/       # UrlRequest, ShortHashRequest
    │   ├── Models/             # Url
    │   ├── Services/           # Бизнес-логика
    │   ├── Bridges/            # Реализации интерфейсов
    │   └── Providers/          # DI-биндинги
    ├── routes/
    │   ├── web.php             # Веб-маршруты
    │   └── api.php             # API-маршруты
    ├── resources/views/        # Blade-шаблоны
    ├── public/                 # Статические файлы (JS, CSS)
    ├── database/migrations/    # Миграции БД
    ├── composer.json
    └── package.json
```

## API

| Метод  | URL                      | Описание                          |
|--------|--------------------------|-----------------------------------|
| GET    | `/`                      | Главная страница                  |
| GET    | `/{shortHash}`           | Редирект на оригинальный URL      |
| POST   | `/api/shorten`           | Создать короткую ссылку           |
| GET    | `/api/url/qr/{shortHash}`| Получить QR-код (PNG)             |

---

## Развёртывание и запуск

### Требования

- [Docker](https://docs.docker.com/get-docker/) и [Docker Compose](https://docs.docker.com/compose/install/)
- (Опционально) Node.js 18+ и npm — для пересборки фронтенда

### 1. Клонировать репозиторий

```bash
git clone <url-репозитория> shortlinker_laravel
cd shortlinker_laravel
```

### 2. Настроить переменные окружения Docker

Файл `docker/.env` уже содержит значения по умолчанию. При необходимости отредактируйте:

```bash
# docker/.env
COMPOSE_PROJECT_NAME=shortlinker_laravel
APP_DOMAIN=short-linker.loc        # Домен приложения
MYSQL_ROOT_PASSWORD=1              # Пароль root MariaDB
MYSQL_DATABASE=shortlinker         # Имя базы данных
NGINX_PORT=80                      # Порт Nginx
MARIADB_PORT=3306                  # Порт MariaDB
```

### 3. Добавить домен в hosts

Добавьте запись в файл hosts, чтобы домен резолвился локально:

**macOS / Linux:**
```bash
sudo sh -c 'echo "127.0.0.1 short-linker.loc" >> /etc/hosts'
```

**Windows** (от имени администратора):
```
echo 127.0.0.1 short-linker.loc >> C:\Windows\System32\drivers\etc\hosts
```

> Если вы изменили `APP_DOMAIN` в `docker/.env`, используйте ваш домен вместо `short-linker.loc`.

### 4. Запустить контейнеры

```bash
cd docker
docker-compose up -d --build
```

Будут подняты три контейнера:
- `shortlinker_php` — PHP 8.3 FPM
- `shortlinker_nginx` — Nginx (проксирует на PHP-FPM)
- `shortlinker_mariadb` — MariaDB 11.4

### 5. Установить зависимости Composer

```bash
docker-compose exec php composer install
```

### 6. Настроить Laravel

```bash
docker-compose exec php cp .env.example .env
docker-compose exec php php artisan key:generate
```

### 7. Настроить переменные Laravel

Отредактируйте `site/.env` при необходимости. Ключевые переменные:

```dotenv
APP_URL=http://short-linker.loc

DB_CONNECTION=mariadb
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=shortlinker
DB_USERNAME=root
DB_PASSWORD=1

SHORT_LINK_TEMPLATE=http://short-linker.loc/{short_hash}
```

> `SHORT_LINK_TEMPLATE` определяет формат коротких ссылок. Замените домен на свой при необходимости.

### 8. Выполнить миграции

```bash
docker-compose exec php php artisan migrate
```

### 9. (Опционально) Собрать фронтенд

Если вам нужно пересобрать CSS/JS:

```bash
docker-compose exec php npm install
docker-compose exec php npm run build
```

### 10. Открыть приложение

Перейдите в браузере по адресу:

```
http://short-linker.loc
```

(или по другому домену/порту, если вы их меняли)

---

## Быстрый старт (одной командой)

Внутри контейнера доступен Composer-скрипт `setup`, который выполняет все шаги автоматически:

```bash
cd docker
docker-compose up -d --build
docker-compose exec php composer setup
```

Скрипт выполнит: `composer install`, копирование `.env`, генерацию ключа, миграции, `npm install` и `npm run build`.

---

## Полезные команды

```bash
# Все команды выполняются из директории docker/

# Остановить контейнеры
docker-compose down

# Просмотр логов
docker-compose logs -f

# Зайти в PHP-контейнер
docker-compose exec php bash

# Artisan-команды
docker-compose exec php php artisan <command>

# Запустить тесты
docker-compose exec php composer test

# Режим разработки (сервер + очереди + логи + Vite HMR)
docker-compose exec php composer dev

# Проверка стиля кода
docker-compose exec php vendor/bin/php-cs-fixer fix --dry-run
docker-compose exec php vendor/bin/pint --test
```

## Настройка портов

Если порты 80 или 3306 заняты, измените их в `docker/.env`:

```bash
NGINX_PORT=8080
MARIADB_PORT=33060
```

Затем перезапустите контейнеры:

```bash
docker-compose down && docker-compose up -d
```

Приложение будет доступно по адресу `http://short-linker.loc:8080`.
