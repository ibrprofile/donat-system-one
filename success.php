<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

use YooKassa\Client;

session_start();

if (isset($_SESSION['payment_id'], $_SESSION['nickname'], $_SESSION['amount'])) {
    $client = new Client();
    $client->setAuth(YOOKASSA_SHOP_ID, YOOKASSA_SECRET_KEY);

    try {
        $payment = $client->getPaymentInfo($_SESSION['payment_id']);

        if ($payment->getStatus() === 'succeeded') {
            // Подключение к базе данных
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($conn->connect_error) {
                throw new Exception('Ошибка подключения к базе данных');
            }

            // Обновление баланса пользователя
            $stmt = $conn->prepare("UPDATE " . DONATE_TABLE . " SET " . DONATE_COLUMN . " = " . DONATE_COLUMN . " + ? WHERE nickname = ?");
            $stmt->bind_param("ds", $_SESSION['amount'], $_SESSION['nickname']);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $success = true;
                $message = "Донат успешно начислен на аккаунт!";
            } else {
                throw new Exception('Ошибка при обновлении баланса');
            }

            $conn->close();
        } else {
            throw new Exception('Платеж не был успешно завершен');
        }
    } catch (Exception $e) {
        $success = false;
        $message = $e->getMessage();
    }

    // Очистка сессии
    unset($_SESSION['payment_id'], $_SESSION['nickname'], $_SESSION['amount']);
} else {
    $success = false;
    $message = 'Недостаточно данных для обработки платежа';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат оплаты</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
</head>
<body>
    <div class="container">
        <h1 class="gta-title">Результат оплаты</h1>
        <div class="<?php echo $success ? 'success-message' : 'error-message'; ?>">
            <i class="<?php echo $success ? 'fas fa-check-circle' : 'fas fa-times-circle'; ?>"></i>
            <p><?php echo $message; ?></p>
        </div>
        <a href="index.html" class="back-button">Вернуться на главную</a>
    </div>
</body>
</html>

