<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>AutoRecipe - manage recipes</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="cookies.js"></script>      <!-- Include the fuctions to work with cookies -->
    <script src="change_recipe_data.js"></script>      <!-- Include the fuction that sends the changed recipe's data -->
    <script src="get_recipe_data.js"></script>      <!-- Include the fuction that gets the recipe's data-->
    <script>
        var checkToken = getCookie('token');
        if(checkToken == '') window.location.href = 'login_page.html';
    </script>
</head>
<body>
    <div>
        <button onclick="window.location.href = 'recipe_management.php'">Back to recipe management</button>
    </div>
    <h1 id="recipe_page_header"></h1>
    <div id="rename_recipe_div" style="padding-bottom: 20px">
        <button id="rename_recipe">Rename recipe</button>
    </div>
    <div id="thumbnail_div">
        <img id="thumbnail" width="300" height="200">
        <button id="change_thumbnail">Change</button>
    </div>
    <h2 id="ingredients">Ingredients</h2>
    <div id="recipe_ingredients"></div>
    <h2 id="description">Description</h2>
    <div id="recipe_description"></div>
    <h2 id="steps">Steps</h2>
    <div id="recipe_steps"></div>

    <script>
        getRecipeData();      // Load the data from the server and present it in the <div>
    </script>

</body>
</html> 