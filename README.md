#!/bin/bash

# Создаем основную директорию плагина
mkdir -p tonna-vinyla/src/{Admin,Api,Models,Services}
mkdir -p tonna-vinyla/assets/{css,js}
mkdir -p tonna-vinyla/languages
mkdir -p tonna-vinyla/tests/Unit
mkdir -p tonna-vinyla/vendor

# Создаем файлы
touch tonna-vinyla/src/Admin/{AdminPage.php,Settings.php}
touch tonna-vinyla/src/Api/DiscogsApi.php
touch tonna-vinyla/src/Models/VinylProduct.php
touch tonna-vinyla/src/Services/{ProductService.php,ImportService.php}
touch tonna-vinyla/assets/css/admin.css
touch tonna-vinyla/assets/js/admin.js
touch tonna-vinyla/languages/tonna-vinyla-ru_RU.po
touch tonna-vinyla/tests/Unit/ProductServiceTest.php
touch tonna-vinyla/{index.php,tonna-vinyla.php,uninstall.php,composer.json,README.md}

# Установка базовых прав доступа
chmod 755 tonna-vinyla
find tonna-vinyla -type d -exec chmod 755 {} \;
find tonna-vinyla -type f -exec chmod 644 {} \;

echo "Структура плагина создана успешно!"