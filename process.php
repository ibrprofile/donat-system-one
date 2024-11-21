<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

use YooKassa\Client;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = $_POST['nickname'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);

    if (empty($nickname) || $amount < MIN_DONATE) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        exit;
    }

    // Подключение к базе данных
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
        exit;
    }

    // Проверка существования аккаунта
    $stmt = $conn->prepare("SELECT id FROM " . DONATE_TABLE . " WHERE nickname = ?");
    $stmt->bind_param("s", $nickname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Аккаунт не найден']);
        exit;
    }

    // Создание платежа в Юкассе
    $client = new Client();
    $client->setAuth(YOOKASSA_SHOP_ID, YOOKASSA_SECRET_KEY);

    try {
        $payment = $client->createPayment(
            array(
                'amount' => array(
                    'value' => $amount,
                    'currency' => 'RUB',
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => 'https://your-site.com/success.php',
                ),
                'capture' => true,
                'description' => "Донат для игрока $nickname",
            ),
            uniqid('', true)
        );

        // Сохранение информации о платеже в сессии
        session_start();
        $_SESSION['payment_id'] = $payment->getId();
        $_SESSION['nickname'] = $nickname;
        $_SESSION['amount'] = $amount;

        // Возвращаем URL для перенаправления на страницу оплаты
        echo json_encode(['success' => true, 'payment_url' => $payment->getConfirmation()->getConfirmationUrl()]);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка при создании платежа']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
}

