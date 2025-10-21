#!/bin/bash

set -e

echo " Запуск инициализации..."

# Убедимся, что рабочая директория правильная
cd /var/www/html || { echo " Не могу перейти в /var/www/html"; exit 1; }

# Проверка на существование .env
if [ ! -f .env ]; then
    echo " Создание .env..."
    cp .env.example .env
else
    echo " .env уже существует"
fi

# Создаём необходимые директории и устанавливаем права
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/logs

# Устанавливаем права
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage



# Проверка на существование composer.lock
if [ ! -f vendor/autoload.php ]; then
    echo "Установка зависимостей..."
    composer install --no-interaction --prefer-dist
else
    echo "Зависимости уже установлены"
fi

# Генерация ключа
if [ ! -f bootstrap/cache/config.php ]; then
    echo " Генерация ключа..."
    php artisan key:generate --quiet
else
    echo " Ключ уже сгенерирован"
fi

sleep 10
# Миграции (только если база ещё не создана)
# Проверяем, есть ли миграция с названием, содержащим create_users_table
if ! php artisan migrate:status --format=json | grep -q '"migration":.*_create_users_table'; then
    echo " Выполнение миграций..."
    php artisan migrate:fresh  --seed
else
    echo " Миграции уже выполнены"
fi

# Проверка на существование node_modules
if [ ! -d node_modules ]; then
    echo " Установка npm..."
    npm ci --silent
fi

# Запускаем Laravel
 php artisan config:clear
 php artisan route:clear
 php artisan cache:clear

# Проверка на существование собранных файлов
if [ ! -f public/build/js/app.js ]; then
    echo " Сборка Vue..."
    npm run build --silent
else
    echo "Vue уже собран"
fi

echo " Инициализация завершена!"
