<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>AutoRecipe - manage account</title>
    <script src="cookies.js"></script>      <!-- Include the fuctions to work with cookies -->
    <script src="change_user_data.js"></script>    <!-- Include the fuction that sends the changed user data to the server and also changes the cookie, reloads the page -->
    <script src="get_user_data.js"></script>    <!-- Include the fuction that loads the user's data from the server depending on what username is saved in the cookie, also loads change buttons -->
    <script>
        var checkToken = getCookie('token');
        if(checkToken == '') window.location.href = 'login_page.html';
    </script>
</head>
<body>

    <h1>Manage account</h1>
    <div id="user_data"></div>
    <button onclick="window.location.href = 'main_menu.php'">Back to main menu</button>

    <script>
        getUserData();      // Load the data from the server and present it in the <div>
    </script>

</body>
</html> 