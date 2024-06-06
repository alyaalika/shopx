<?php
session_start();
require '../db/dp.php'; // Подключение к базе данных

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'], $_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Подготовка SQL запроса для выбора пользователя по имени пользователя
        $stmt = $conn->prepare('SELECT * FROM admin WHERE Username = ?');
        $stmt->bind_param('s', $username); // Привязка параметров
        $stmt->execute(); // Выполнение запроса
        $result = $stmt->get_result(); // Получение результирующего набора

        if ($result->num_rows > 0) {
            // Пользователь найден, получение деталей пользователя
            $user = $result->fetch_assoc();

            // Проверка пароля
            if (password_verify($password, $user['Password'])) {
                // Пароль совпадает, установка переменных сессии
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                header('Location: admin.php'); // Перенаправление на страницу администратора
                exit();
            } else {
                // Неверный пароль
                $error_message = 'Неверное имя пользователя или пароль.';
            }
        } else {
            // Пользователь не найден
            $error_message = 'Неверное имя пользователя или пароль.';
        }
    } else {
        // Отсутствуют отправленные данные
        $error_message = 'Отсутствуют данные для входа.';
    }
}
?>
