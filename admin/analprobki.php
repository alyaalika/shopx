
<?php
session_start();

// Include the configuration file
include('../config/dp.php');

// Check if the logout parameter is set
if (isset($_GET['logout'])) {
    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page or any other desired page after logout
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Подключение стилей Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <title>Админка</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="admin_panel.php">Админка</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="admin_panel.php">Товары <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="glav.php">Члены</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cart.php">Смазки</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="log.php">Каталог</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="news.php">Новости</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <div style="text-align: center;">
        <?php 
        if (isset($_SESSION['login'])) {
            echo "Вы в Админке " . $_SESSION['login'];
            // Add the logout button
            echo '<br><a href="?logout=1" class="btn btn-danger">Выход</a>';
        } else {
            echo "Вы не авторизованы";
        }
        ?>
    </div>

    <!-- Форма для загрузки товара -->
    <form method="post" enctype="multipart/form-data" action="uplad1.php" class="mt-4">
        <div class="form-group">
            <label for="productName">Название товара:</label>
            <input type="text" class="form-control" id="productName" name="productName" required>
        </div>
        <div class="form-group">
            <label for="productDescription">Описание товара:</label>
            <textarea class="form-control" id="productDescription" name="productDescription" required></textarea>
        </div>
        <div class="form-group">
            <label for="productPrice">Цена товара:</label>
            <input type="text" class="form-control" id="productPrice" name="productPrice" required>
        </div>
        <div class="form-group">
            <label for="productImage">Изображение товара:</label>
            <input type="file" class="form-control-file" id="productImage" name="productImage" required>
        </div>
        <button type="submit" class="btn btn-primary">Загрузить товар</button>
    </form>
    <!-- Конец формы для загрузки товара -->

    <!-- Карточки на Bootstrap -->
    <div class="row mt-4">
        <!-- PHP код для отображения товаров -->
        <?php
        // Подключение к базе данных и выполнение запроса для получения товаров
        $query = "SELECT * FROM products1";
        $result = mysqli_query($conn, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="' . htmlspecialchars($row['image_path'], ENT_QUOTES, 'UTF-8') . '" class="card-img-top" alt="Изображение товара">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</h5>
                                <p class="card-text">' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</p>
                                <p class="card-text">Цена: ' . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . '</p>
                            </div>
                        </div>
                    </div>';
            }
        } else {
            echo "Ошибка выполнения запроса: " . mysqli_error($conn);
        }
        ?>
        <!-- Конец PHP кода -->
    </div>
    <!-- Конец блока с карточками -->
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // JavaScript function to handle project deletion
    function deleteProject(projectName) {
        // AJAX request to the server to delete the project
        $.ajax({
            type: "POST",
            url: "delete_project.php", // Provide the correct path to your delete_project.php script
            data: { project_name: projectName },
            success: function(response) {
                // Handle the response, e.g., refresh the page
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error(xhr.responseText);
            }
        });
    }

    // JavaScript function to handle project addition
    function addProject(projectName) {
        // Implement the logic for adding a project
        console.log("Add button clicked for project: " + projectName);
        // You can open a modal or perform any other action to add a project
    }
</script>
</body>
</html>
