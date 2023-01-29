<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>AutoRecipe - manage recipes</title>
    <script src="cookies.js"></script>      <!-- Include the fuctions to work with cookies -->
    <script src="change_recipe_data.js"></script>
    <script src="create_recipe.js"></script>      <!-- Include the fuction that gets already created recipes list -->
    <script src="get_recipe_list.js"></script>      <!-- Include the fuction that gets already created recipes list -->
    <script>
        var checkToken = getCookie('token');
        if(checkToken == '') window.location.href = 'login_page.html';
    </script>
</head>
<body>

    <h1>Manage recipes</h1>
    <div id="created_recipes"></div>
    <button id = "create a new recipe" onclick=createRecipe()>Create a new recipe</button>
    <span id="new_recipe_inputs"></span>
    <div>
        <button onclick="window.location.href = 'main_menu.php'">Back to main menu</button>
    </div>

    <script>
        getRecipeList();      // Load the data from the server and present it in the <div>
    </script>

</body>
</html> 