<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <title>Админ панель</title>
</head>
<body>
<div class="muka">
    <h1 style="color: white;">Админка</h1>
</div>
<div class="red">
    <h2>Login</h2>
    <form action="admin.php" method="post">
        <label for="login">login:</label>
        <input type="text" id="log" name="log">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password">

        <div class="remember-me">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Remember me</label>
        </div>

        <input type="submit" value="Login" name="login">
    </form>
</div>
</body>
</html>
