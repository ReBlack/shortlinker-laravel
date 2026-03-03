# Docker конфигурация для Laravel проекта

## Состав

- **PHP 8.3** с FPM и необходимыми расширениями для Laravel
- **Nginx** как веб-сервер
- **MariaDB 11.4** как база данных

## Быстрый старт

1. Запустить контейнеры:
```bash
cd docker
docker-compose up -d
```

2. Установить зависимости Composer (внутри контейнера PHP):
```bash
docker-compose exec php composer install
```

3. Настроить Laravel:
```bash
docker-compose exec php cp .env.example .env
docker-compose exec php php artisan key:generate
```

4. Применить миграции:
```bash
docker-compose exec php php artisan migrate
```

## Доступ

- **Веб-приложение**: URL настраивается в файле `.env` через переменную `APP_DOMAIN` (по умолчанию: http://short-linker.loc)

**Настройка URL:** Откройте файл `.env` и измените значение `APP_DOMAIN` на нужный домен. После изменения перезапустите контейнер nginx:
```bash
docker-compose restart nginx
```

**Важно:** Для работы домена добавьте в файл `/etc/hosts` (на macOS/Linux) или `C:\Windows\System32\drivers\etc\hosts` (на Windows) строку:
```
127.0.0.1 short-linker.loc
```
(Используйте значение из `.env` переменной `APP_DOMAIN`)
- **MariaDB**: localhost:3306 (порт настраивается в `.env`)
  - База данных: `shortlinker` (настраивается в `.env`)
  - Пользователь: `root`
  - Пароль: `1` (настраивается в `.env`)

## Полезные команды

Остановить контейнеры:
```bash
docker-compose down
```

Просмотр логов:
```bash
docker-compose logs -f
```

Выполнить команду в PHP контейнере:
```bash
docker-compose exec php bash
```

Artisan команды:
```bash
docker-compose exec php php artisan <command>
```

## Настройка базы данных

Параметры подключения к БД в Laravel `.env`:
```
DB_CONNECTION=mariadb
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=shortlinker
DB_USERNAME=root
DB_PASSWORD=1
```

## Переменные окружения

Все настройки можно изменить в файле `.env`:

### Настройка URL приложения
- `APP_DOMAIN` - **домен приложения** (по умолчанию: short-linker.loc). Измените эту переменную для настройки URL приложения.

### Настройка портов
- `NGINX_PORT` - порт для веб-сервера (по умолчанию: 80)
- `MARIADB_PORT` - порт для MariaDB (по умолчанию: 3306)

### Настройка базы данных
- `MYSQL_ROOT_PASSWORD` - пароль root пользователя (по умолчанию: 1)
- `MYSQL_DATABASE` - имя базы данных (по умолчанию: shortlinker)
