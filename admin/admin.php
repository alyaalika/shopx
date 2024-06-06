<?php
session_start(); // Начинаем сессию

require_once('../config/dp.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['log'];
    $password = $_POST['password'];

    // Retrieve hashed password from the database
    $query = "SELECT * FROM admin WHERE login = '$login'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            // Check password
            if ($password === $row['password']) {
                // Successful login
                $_SESSION['login'] = $login; // Сохраняем логин в сессии
                
                // Redirect to the admin panel
                header("Location: admin_panel.php");
                exit();
            } else {
                // Incorrect password
                echo "Incorrect login or password";
            }
        } else {
            // User not found
            echo "Incorrect login or password";
        }
    } else {
        // Database query error
        echo "Error executing query: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
