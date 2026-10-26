# Мини-сервис заметок

Backend: Laravel 12, PHP 8.4, PostgreSQL 16.  
Frontend: Vue 3, TypeScript, Vite.  
Инфраструктура: Docker Compose, Nginx.

## Запуск

Требуется Docker с Docker Compose.

Создайте файл `.env` в корне проекта на основе `.env.example` и укажите `APP_KEY`.

Сгенерировать ключ можно командой:

```bash
docker run --rm php:8.4-cli-alpine php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

Запустите приложение:

```bash
docker compose up -d --build
```

После запуска доступны:

- Приложение: http://localhost:8080
- API: http://localhost:8080/api/v1/notes
- Swagger UI: http://localhost:8080/docs/
- OpenAPI: http://localhost:8080/openapi.yaml

Миграции базы данных выполняются автоматически при запуске backend-контейнера.

## API 
| Метод | URL | Описание               |
|---|---|------------------------|
| GET | `/api/v1/notes` | Вывод всех заметок     |
| POST | `/api/v1/notes` | Создание заметки       |
| GET | `/api/v1/notes/{id}` | Просмотр заметки       |
| PUT | `/api/v1/notes/{id}` | Редактирование заметки |
| DELETE | `/api/v1/notes/{id}` | Удаление заметки       |

Для создания и обновления используются поля `title` и `content`. Оба обязательны.

## Тесты

Запуск PHPUnit в отдельном Docker-контейнере:

```bash
docker compose --profile test run --rm --build tests
```

Тесты используют отдельную БД SQLite

## Структура проекта

```text
backend/   Laravel API
frontend/  Vue-приложение
docker/    Dockerfile и конфигурация Nginx
docs/      Спецификация OpenAPI
```

## Остановка

```bash
docker compose down
```

Данные PostgreSQL сохраняются в Docker volume.
