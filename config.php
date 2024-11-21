<?php
// Данные подключения к базе данных
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database');

// Таблица и столбец для начисления доната
define('DONATE_TABLE', 'accounts');
define('DONATE_COLUMN', 'donate_balance');

// Минимальная сумма доната
define('MIN_DONATE', 10);

// Данные платежной системы (Юкасса)
define('YOOKASSA_SHOP_ID', 'your_shop_id');
define('YOOKASSA_SECRET_KEY', 'your_secret_key');

