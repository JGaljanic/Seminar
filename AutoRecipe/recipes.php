<?php
$DEBUG = true;  // Debug mode (return detailed error messages)

include("tools.php"); 

$database = dbConnect();	// Create a connection to the DB

header('Content-Type: application/json');	// Nastavimo MiME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

$headers = apache_request_headers();

if(isset($headers['Authorization'])) {
	$headers = trim($headers["Authorization"]);
	if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        $token = $matches[1];
        $query_check_saved_token = "SELECT token FROM user_data WHERE token = '$token'";
        $check_saved_token = mysqli_query($database, $query_check_saved_token);
        if(mysqli_num_rows($check_saved_token) > 0) {
            switch($_SERVER["REQUEST_METHOD"])
            {
                case 'POST':
                    add_recipe();
                    break;
                case 'DELETE':
                    if(!empty($_GET["recipe_ID"]))
                    {
                        delete_recipe($_GET["recipe_ID"]);
                    }
                    else
                    {
                        http_response_code(400);	// Bad Request
                    }
                    break;
                case 'GET':
                    if(!empty($_GET["ingredient1"]))
                    {
                        search_recipes();
                    }
                    else if (!empty($_GET["recipe_ID"]))
                    {
                        get_recipe($_GET["recipe_ID"]);
                    }
                    else if(!empty($_GET["nickname"]))
                    {
                        get_user_recipes($_GET["nickname"]);
                    }
                    else
                    {
                        http_response_code(400);    // Bad request
                    }
                    break;
                case 'PUT':
                    if(!empty($_GET["recipe_ID"]))
                    {
                        edit_recipe($_GET["recipe_ID"]);
                    }
                    else
                    {
                        http_response_code(400);	// Bad Request
                    }
                    break;
                default:
                    http_response_code(405);	// Method Not Allowed
                    break;
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

mysqli_close($database);    // Terminate connection to the DB

// ----------- konec skripte, sledijo funkcije -----------

function add_recipe() {
	global $database;
	
    $inserted_recipe = json_decode(file_get_contents('php://input'), true);

    if(isset($inserted_recipe["recipe_name"], $inserted_recipe["ingredients"], $inserted_recipe["data"], $inserted_recipe["added_by"])) {

        // Extract data from the HTTP input
        $recipe_name = mysqli_escape_string($database, $inserted_recipe["recipe_name"]);            
        $recipe_ingredients = $inserted_recipe["ingredients"];
        $added_by = $inserted_recipe["added_by"];

        // Get the next recipe's index
        $query_next_recipe_index = "SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'auto_recipe' AND TABLE_NAME = 'recipes'";
        $next_recipe_index = mysqli_query($database, $query_next_recipe_index);

        while($row = mysqli_fetch_assoc($next_recipe_index)) {
            $queried_recipe_index[] = $row;
        }

        $recipe_index = $queried_recipe_index[0]["AUTO_INCREMENT"];
        
        // Add the steps' picture path to data
        for($i = 1; $i <= count($inserted_recipe["data"]) - 1; $i++) {
            $inserted_recipe["data"]["step" . $i]["step" . $i . "_picture"] = "DB_data/Pictures/$recipe_index/step" . $i . ".jpg";
        }
        $recipe_data = addslashes(json_encode($inserted_recipe["data"]));

        // Insert the new recipe data into the recipes table of the DB
        $query_add_recipe = "INSERT INTO recipes (recipe_name, recipe_data, recipe_thumbnail, added_by) VALUES ('$recipe_name', '$recipe_data', 'DB_data/Pictures/$recipe_index/thumbnail.jpg', '$added_by')";        
        $add_recipe = mysqli_query($database, $query_add_recipe);

        // Create the folder for pictures
        mkdir(realpath($_SERVER["DOCUMENT_ROOT"]) . "\\AutoRecipe\\" . "DB_data/Pictures/$recipe_index");
        
        // Insert new ingredients into ingredients table
        $number_of_ingredients = count($recipe_ingredients);
        for($ingredient_index = 1; $ingredient_index <= $number_of_ingredients; $ingredient_index++) {
            
            $ingredient_name = strtolower($recipe_ingredients["ingredient" . "$ingredient_index"]);

            // Check if the ingredient already exists in DB
            $query_ingredient_exists_in_DB = "SELECT ingredient_name FROM ingredients WHERE ingredient_name = '$ingredient_name'";         
            $ingredient_exists_in_DB = mysqli_query($database, $query_ingredient_exists_in_DB);

            $ingredient_exists = false;
            while($row = mysqli_fetch_row($ingredient_exists_in_DB)) {
                if(!strcmp($ingredient_name, $row[0])) {
                    $ingredient_exists = true;
                    break;
                }
            }

            // Insert the new recipe's ingredient into the ingredients table of the DB if it doesn't exist there yet
            if($ingredient_exists == false) {
                $query_add_ingredient = "INSERT INTO ingredients (ingredient_name) VALUES ('$ingredient_name')";        
                $add_ingredient = mysqli_query($database, $query_add_ingredient);
                $ingredient_exists = false;
            }
        }

        // Insert recipe-ingredient connections
        $query_new_recipe_ID = "SELECT recipe_ID FROM recipes WHERE recipe_name = '$recipe_name'";
        $new_recipe_ID = mysqli_fetch_row(mysqli_query($database, $query_new_recipe_ID))[0];

        $query_ingredients_IDs = "SELECT ingredient_ID FROM ingredients WHERE ingredient_name = ";
        for($ingredient_index = 1; $ingredient_index <= $number_of_ingredients; $ingredient_index++) {
            $ingredient_name = strtolower($recipe_ingredients["ingredient" . "$ingredient_index"]);
            if($ingredient_index == 1) {
                $query_ingredients_IDs = $query_ingredients_IDs . "'$ingredient_name'";
            }
            else {
                $query_ingredients_IDs = $query_ingredients_IDs . " OR ingredient_name = '$ingredient_name'";
            }
        }
        $ingredients_IDs = mysqli_query($database, $query_ingredients_IDs);
        
        while($row = mysqli_fetch_row($ingredients_IDs)) {
            $current_ingredient_ID = $row[0];
            // Check if this connection already exsists
            $query_connection_exists = "SELECT connection_ID FROM ingredients_for_recipes WHERE recipe_ID = '$new_recipe_ID' AND ingredient_ID = '$current_ingredient_ID'";
            $connection_exists = mysqli_query($database, $query_connection_exists);

            if(mysqli_fetch_row($connection_exists) == NULL) {
                $query_insert_new_connections = "INSERT INTO ingredients_for_recipes (recipe_ID, ingredient_ID) VALUES ('$new_recipe_ID', '$current_ingredient_ID')";
                $insert_new_connections = mysqli_query($database, $query_insert_new_connections);
            }
        }
        return 0;
    }
    else {
        http_response_code(400);    // Bad Request
        echo "Error - an unset variable in HTTP input";
        return 0;
    }
}

function delete_recipe($recipe_ID) {
    global $database;

    // Delete the recipe picture folder
    $query_get_pictures_folder = "SELECT recipe_thumbnail FROM recipes WHERE recipe_ID = '$recipe_ID'";
    $get_pictures_folder = mysqli_query($database, $query_get_pictures_folder);

    while($row = mysqli_fetch_assoc($get_pictures_folder)) {
        $pictures_folder_object[] = $row;
    }

    $pictures_folder = str_replace("/", "\\", $pictures_folder_object[0]["recipe_thumbnail"]);

    $pictures_folder = dirname(realpath($_SERVER["DOCUMENT_ROOT"]) . "\\AutoRecipe\\" . $pictures_folder) . "\\";

    if (is_dir($pictures_folder)) { 
        $objects = scandir($pictures_folder);
        foreach ($objects as $object) { 
          if ($object != "." && $object != "..") { 
            if (is_dir($pictures_folder. DIRECTORY_SEPARATOR .$object) && !is_link($pictures_folder."/".$object))
              rrmdir($pictures_folder. DIRECTORY_SEPARATOR .$object);
            else
              unlink($pictures_folder. DIRECTORY_SEPARATOR .$object); 
          } 
        }
        rmdir($pictures_folder);
    }

    // Delete recipe content in DB
    $query_delete_recipe = "DELETE FROM recipes WHERE recipe_ID = '$recipe_ID'";
    $delete_recipe = mysqli_query($database, $query_delete_recipe);

    if($delete_recipe !== false) {
		http_response_code(200);                // OK
	}
	else {
		http_response_code(404);        // Recipe ID not found
        echo "Recipe ID not found";
        return 0;
    }

    // Delete ingredient connections with deleted recipe
    $query_delete_connections = "DELETE FROM ingredients_for_recipes WHERE recipe_ID = '$recipe_ID'";
    if(mysqli_query($database, $query_delete_connections)) {
		http_response_code(200);            // OK
	}
	else {
		http_response_code(500);            // Internal Server Error
        return 0;
    }
}

function search_recipes() {
    global $database;
	
    // Get ingredients indices from received ingredient names
	$inserted_ingredients = $_GET;
	$query_ingredient_indices = "SELECT ingredient_ID FROM ingredients WHERE ingredient_name = ";
	$inserted_ingredient_index = 1;
	$number_of_inserted_ingredients = count($inserted_ingredients);
    
    // Create the SQL query for ingredient indices
	while($inserted_ingredient_index <= $number_of_inserted_ingredients) {									
		$ingredient = $inserted_ingredients["ingredient" . "$inserted_ingredient_index"];
		if($inserted_ingredient_index == 1) {
			$query_ingredient_indices = $query_ingredient_indices . "'$ingredient'";
		}
		else {
			$query_ingredient_indices = $query_ingredient_indices . " OR ingredient_name = '$ingredient'";
		}
		$inserted_ingredient_index = $inserted_ingredient_index + 1;
	}

	$ingredient_indices = mysqli_query($database, $query_ingredient_indices);

    if(mysqli_num_rows($ingredient_indices) == 0) {
        http_response_code(404);		// Not Found
		echo "Error - ingredients not found";
		return 0;
    }
	
    // Find the indices of the recipes that contain the inserted ingredients
    $query_recipe_indices = "SELECT recipe_ID FROM ingredients_for_recipes WHERE ingredient_ID = ";			
	$first_recipe_index_found = false;
	while($row = mysqli_fetch_assoc($ingredient_indices)) {											
		$ingredient_index = $row["ingredient_ID"];
		if($first_recipe_index_found) {
			$query_recipe_indices = $query_recipe_indices . " OR ingredient_ID = $ingredient_index";
		}
	    else {
			$query_recipe_indices = $query_recipe_indices . "$ingredient_index";
			$first_recipe_index_found = true;
		}
	}

	$recipe_indices = mysqli_query($database, $query_recipe_indices);

    // Find out how many inputted ingredients every found recipe contains (to compare with the number of all of the recipe's ingredients at the end)
    while($row = mysqli_fetch_assoc($recipe_indices)) {
        $recipe_indices_sql_array[] = $row;
    }
    $recipe_indices_array = array();
    foreach ($recipe_indices_sql_array as $item) {
        $recipe_indices_array[] = $item['recipe_ID'];
    }

    $number_of_relevant_ingredients = array();
    foreach ($recipe_indices_array as $item) {
        if (isset($number_of_relevant_ingredients[$item])) {
            $number_of_relevant_ingredients[$item]++;
        } else {
            $number_of_relevant_ingredients[$item] = 1;
        }
    }

    $query_recipe_indices = $query_recipe_indices . " GROUP BY recipe_ID";

    // Use the recipe indices to create a JSON list of recipe names for the front end's lists
    $query_recipe_names = "SELECT t2.recipe_ID, t2.recipe_name, t2.recipe_thumbnail 
                            FROM ($query_recipe_indices) AS t1 
                            JOIN recipes AS t2 
                            ON t1.recipe_ID = t2.recipe_ID";
    $recipe_names = mysqli_query($database, $query_recipe_names);

    if ($recipe_names == false) {														
		http_response_code(404);		// Not Found
		echo "Error - recipes not found";
		return 0;
	}

    while($row = mysqli_fetch_assoc($recipe_names)) {
        $all_recipe_names[] = $row;
    }

    // Order the result by relevancy (number of relevant inputted ingredients divided by number of all ingredients in a recipe)
    for($i = 0; $i < sizeof($all_recipe_names); $i++) {
        $recipe_ID = $all_recipe_names[$i]["recipe_ID"];
        $query_number_of_recipe_ingredients = "SELECT count(*) FROM ingredients_for_recipes WHERE recipe_ID = $recipe_ID";
        $number_of_recipe_ingredients = mysqli_query($database, $query_number_of_recipe_ingredients);
        while($row = mysqli_fetch_assoc($number_of_recipe_ingredients)) {
            $count_of_recipe_ingredients[] = $row;
        }
        $relevancy_factors[$recipe_ID] = $number_of_relevant_ingredients[$recipe_ID]/$count_of_recipe_ingredients[$i]["count(*)"];
    }

    arsort($relevancy_factors);
    $relevancy_factors = array_keys($relevancy_factors);    // Because the recipe_ID values are saved as keys and because only their order is needed transform the array so it only has the recipe_ID values

    $all_recipe_names_ordered = array();
    $j = 0;
    foreach($relevancy_factors as $item) {
        for($i = 0; $i < sizeof($all_recipe_names); $i++) {
            if($all_recipe_names[$i]["recipe_ID"] == $item) $all_recipe_names_ordered[$j] = $all_recipe_names[$i];
        }
        $j++;
    }

    http_response_code(200);		//OK
	echo json_encode($all_recipe_names_ordered);
}

function get_recipe($recipe_ID) {
    global $database;

    // Use the sent recipe_ID to query JSON recipe data (steps + description)
    $query_recipe_data = "SELECT recipe_data FROM recipes WHERE recipe_ID = '$recipe_ID'";
	
    $recipe_data = mysqli_fetch_row(mysqli_query($database, $query_recipe_data))[0];
	
	if ($recipe_data == false) {														
		http_response_code(404);		// Not Found
		echo "Error - recipe steps not found";
		return 0;
	}
	
	http_response_code(200);		//OK
	echo $recipe_data;
}

function get_user_recipes($nickname) {
    global $database;

    $get_user_recipe_list_query = "SELECT recipe_name, recipe_ID FROM recipes WHERE added_by = '$nickname'";
    $get_user_recipe_list = mysqli_query($database, $get_user_recipe_list_query);

    if ($get_user_recipe_list == false) {														
		http_response_code(404);		// Not Found
		echo "Error - recipes not found";
		return 0;
	}

    while($row = mysqli_fetch_assoc($get_user_recipe_list)) {
        $all_recipe_names[] = $row;
    }

    http_response_code(200);		//OK
	echo json_encode($all_recipe_names);
}

function edit_recipe($recipe_ID) {

    global $database;
	
    $inserted_recipe = json_decode(file_get_contents('php://input'), true);

    if(isset($inserted_recipe["new_recipe_name"], $inserted_recipe["ingredients"], $inserted_recipe["data"])) {

        // Extract data from the HTTP input
        $new_recipe_name = mysqli_escape_string($database, $inserted_recipe["new_recipe_name"]);            
        $new_recipe_ingredients = $inserted_recipe["ingredients"];
        $new_recipe_data = addslashes(json_encode($inserted_recipe["data"]));

        $query_update_recipe = "UPDATE recipes SET recipe_name = '$new_recipe_name', recipe_data = '$new_recipe_data' WHERE recipe_ID = '$recipe_ID'";     // insert the new recipe data into the recipes table of the DB
        $update_recipe = mysqli_query($database, $query_update_recipe);

        if($update_recipe = false) {
            http_response_code(404);    // ID not found
            echo "ID was not found in the database";
            return 0;
        }
        
        // Insert new ingredients into ingredients table
        $number_of_ingredients = count($new_recipe_ingredients);
        for($ingredient_index = 1; $ingredient_index <= $number_of_ingredients; $ingredient_index++) {
            
            $ingredient_name = strtolower($new_recipe_ingredients["ingredient" . "$ingredient_index"]);

            $query_ingredient_exists_in_DB = "SELECT ingredient_name FROM ingredients WHERE ingredient_name = '$ingredient_name'";          //check if the ingredient already exists in DB
            $ingredient_exists_in_DB = mysqli_query($database, $query_ingredient_exists_in_DB);

            $ingredient_exists = false;
            while($row = mysqli_fetch_row($ingredient_exists_in_DB)) {
                if(!strcmp($ingredient_name, $row[0])) {
                    $ingredient_exists = true;
                    break;
                }
            }

            if($ingredient_exists == false) {
                $query_add_ingredient = "INSERT INTO ingredients (ingredient_name) VALUES ('$ingredient_name')";        // insert the new recipe's ingredient into the ingredients table of the DB if it doesn't exist there yet
                $add_ingredient = mysqli_query($database, $query_add_ingredient);
                $ingredient_exists = false;
            }
        }

        // Insert new connections
        $query_ingredients_IDs = "SELECT ingredient_ID FROM ingredients WHERE ingredient_name = ";
        for($ingredient_index = 1; $ingredient_index <= $number_of_ingredients; $ingredient_index++) {
            $ingredient_name = strtolower($new_recipe_ingredients["ingredient" . "$ingredient_index"]);
            if($ingredient_index == 1) {
                $query_ingredients_IDs = $query_ingredients_IDs . "'$ingredient_name'";
            }
            else {
                $query_ingredients_IDs = $query_ingredients_IDs . " OR ingredient_name = '$ingredient_name'";
            }
        }
        $ingredients_IDs = mysqli_query($database, $query_ingredients_IDs);
        $ingredients_IDs_array = array();
        
        while($row = mysqli_fetch_row($ingredients_IDs)) {
            $current_ingredient_ID = $row[0];
            // Check if this connection already exsists
            $query_connection_exists = "SELECT connection_ID FROM ingredients_for_recipes WHERE recipe_ID = '$recipe_ID' AND ingredient_ID = '$current_ingredient_ID'";
            $connection_exists = mysqli_query($database, $query_connection_exists);

            if(mysqli_fetch_row($connection_exists) == NULL) {
                $query_insert_new_connections = "INSERT INTO ingredients_for_recipes (recipe_ID, ingredient_ID) VALUES ('$recipe_ID', '$current_ingredient_ID')";
                $insert_new_connections = mysqli_query($database, $query_insert_new_connections);
            }
            // Add the ingredient ID to the array of currently valid ingredients so that obsolete ingredients can be deleted from the connections table
            array_push($ingredients_IDs_array, $current_ingredient_ID);
        }
        // Delete the obsolete recipe-ingredient connections if needed
        if(!empty($ingredients_IDs_array)) {
            // Create the SQL query
            $query_delete_connections = "DELETE FROM ingredients_for_recipes WHERE recipe_ID = '$recipe_ID' AND ingredient_ID NOT IN (";
            for($i = 0; $i < count($ingredients_IDs_array); $i++) {
                if($i == count($ingredients_IDs_array) - 1) {
                    $query_delete_connections = $query_delete_connections . $ingredients_IDs_array[$i] . ')';
                }
                else {
                    $query_delete_connections = $query_delete_connections . $ingredients_IDs_array[$i] . ', ';
                }
            }

            $delete_connections = mysqli_query($database, $query_delete_connections);
            
            if($delete_connections == false) {
                http_response_code(500);        // Internal Server Error
                echo "Failed to delete the obsolete ingredients";
                return 0;
            }
        }
        
        http_response_code(200);    // OK
        return 0;
    }
    else {
        http_response_code(400);    // Bad Request
        echo "Error - an unset variable in HTTP input";
        return 0;
    }
}

?>