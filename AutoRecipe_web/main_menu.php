<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>AutoRecipe</title>
    <script src="cookies.js"></script>      <!-- Include the fuctions to work with cookies -->
    <script>
        var checkToken = getCookie('token');
        if(checkToken == '') window.location.href = 'login_page.html';
    </script>
</head>
<body>

    <h1>Main menu</h1>
    <a href="account_management.php">Manage your account</a>        <!-- Change username, password, email and delete account -->
    <a href="recipe_management.php">Manage recipes</a>              <!-- Add, edit, delete your recipes -->
    <button onclick="window.location.href = 'login_page.html'; setCookie('nickname', '', -1); setCookie('token', '', -1)">Log out</button>      <!-- Log out and delete the cookie -->

</body>
</html> 