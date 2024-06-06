<?php
// Подключение к базе данных
include('../config/dp.php');

// Проверяем, был ли отправлен файл
if(isset($_FILES['productImage'])) {
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $productPrice = $_POST['productPrice'];

    // Директория, куда будут загружены изображения
    $targetDirectory = "../uploads/";
    // Полный путь к загруженному файлу
    $targetFile = $targetDirectory . basename($_FILES['productImage']['name']);
    // Переменная, указывающая, был ли файл успешно загружен
    $uploadOk = 1;
    // Получаем тип файла
    $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));

    // Проверяем, является ли файл изображением
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES['productImage']['tmp_name']);
        if($check !== false) {
            echo "Файл является изображением - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "Файл не является изображением.";
            $uploadOk = 0;
        }
    }

    // Проверяем, существует ли файл уже
    if (file_exists($targetFile)) {
        echo "Извините, файл уже существует.";
        $uploadOk = 0;
    }

    // Проверяем размер файла
    if ($_FILES['productImage']['size'] > 500000) {
        echo "Извините, ваш файл слишком большой.";
        $uploadOk = 0;
    }

    // Разрешенные форматы файлов
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Извините, только JPG, JPEG, PNG и GIF файлы разрешены.";
        $uploadOk = 0;
    }

    // Проверяем, был ли установлен флаг ошибки
    if ($uploadOk == 0) {
        echo "Извините, ваш файл не был загружен.";
    // Если все в порядке, пытаемся загрузить файл
    } else {
        if (move_uploaded_file($_FILES['productImage']['tmp_name'], $targetFile)) {
            echo "Файл ". basename( $_FILES['productImage']['name']). " успешно загружен.";

            // Вставляем информацию о товаре в базу данных
            $query = "INSERT INTO products41 (name, description, price, image_path) VALUES ('$productName', '$productDescription', '$productPrice', '$targetFile')";
            if(mysqli_query($conn, $query)) {
                echo "Информация о товаре успешно добавлена в базу данных.";
            } else {
                echo "Ошибка: " . $query . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Произошла ошибка при загрузке файла.";
        }
    }
} else {
    echo "Ошибка: файл не был отправлен.";
}
?>
