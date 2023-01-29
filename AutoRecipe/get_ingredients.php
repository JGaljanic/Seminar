<?php

$DEBUG = true;

include("tools.php");

$database = dbConnect();    // Connect to the database using the function in tools.php

header('Access-Control-Allow-Origin: *');	// Allow access outside of current domain (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		// Add other request types (by default only GET and POST are allowed)

$headers = apache_request_headers();

if(isset($headers['Authorization'])) {
	$headers = trim($headers["Authorization"]);
	if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        $token = $matches[1];
        $query_check_saved_token = "SELECT token FROM user_data WHERE token = '$token'";
        $check_saved_token = mysqli_query($database, $query_check_saved_token);
        if(mysqli_num_rows($check_saved_token) > 0) {
            if ($_SERVER["REQUEST_METHOD"] == 'GET' && !empty($_GET["recipe_ID"])) {
                $recipe_ID = $_GET['recipe_ID'];

                $query_get_ingredient_IDs = "SELECT ingredient_ID FROM ingredients_for_recipes WHERE recipe_ID = '$recipe_ID'";
                $get_ingredient_IDs = mysqli_query($database, $query_get_ingredient_IDs);

                $first_ingredient_found = false;
                while($row = mysqli_fetch_assoc($get_ingredient_IDs)) {
                    $ingredient_ID = $row["ingredient_ID"];
                    if($first_ingredient_found == false) {
                        $query_get_ingredients = "SELECT ingredient_name FROM ingredients WHERE ingredient_ID = '$ingredient_ID'";
                        $first_ingredient_found = true;
                    }
                    else {
                        $query_get_ingredients = $query_get_ingredients . " OR ingredient_ID = '$ingredient_ID'";
                    }
                }
                $get_ingredients = mysqli_query($database, $query_get_ingredients);

                while($row = mysqli_fetch_assoc($get_ingredients)) {
                    $ingredients[] = $row;
                }

                $ingredients_array = array();
                $i = 1;
                foreach ($ingredients as $item) {
                    $ingredients_array["ingredient" . strval($i)] = $item['ingredient_name'];
                    $i++;
                }

                http_response_code(200);		//OK
                echo json_encode($ingredients_array);
            }
            else {
                http_response_code(405);	// Method not allowed
                exit;
            }
        }
		else{
			http_response_code(401);
		}
	}
	else{
		http_response_code(401);
	}
}
else{
	http_response_code(401);
}

mysqli_close($database);	// Disconnect from the database

?>