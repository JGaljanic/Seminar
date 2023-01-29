<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>AutoRecipe - create account</title>
    <script src="create_account.js"></script>   <!-- Include the function to convert form into JSON string to send to the server. -->
</head>
<body>

    <h1>Create account</h1>
    <form id="form" onsubmit="createAccount(); return false;">
        <input type="text" placeholder="nickname" name="nickname" required>
        <input type="password" placeholder="password" name="password" required>
        <input type="text" placeholder="email" name="email" required>
        <button type="submit">Create account</button>
    </form>
    <button onclick="window.location.href = 'login_page.html'">Back to login page</button>
</body>
</html> 