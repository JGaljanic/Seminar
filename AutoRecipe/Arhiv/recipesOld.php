<?php
$DEBUG = true;  // Debug mode (return detailed error messages)

include("tools.php"); 

$database = dbConnect();	// Create a connection to the DB

header('Content-Type: application/json');	// Nastavimo MiME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

switch($_SERVER["REQUEST_METHOD"])
{
	case 'POST':
		add_recipe();
		break;
    case 'DELETE':
		if(!empty($_GET["recipe_name"]))
		{
			delete_recipe($_GET["recipe_name"]);
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
        else if (!empty($_GET["recipe_name"]))
        {
            get_recipe($_GET["recipe_name"]);
        }
        else
        {
            http_response_code(400);    // Bad request
        }
        break;
    case 'PUT':
        if(!empty($_GET["recipe_name"]))
		{
			edit_recipe($_GET["recipe_name"]);
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

mysqli_close($database);    // Terminate connection to the DB

// ----------- konec skripte, sledijo funkcije -----------

function add_recipe() {
	global $database;
	
    $inserted_recipe = json_decode(file_get_contents('php://input'), true);

    if(isset($inserted_recipe["recipe_name"], $inserted_recipe["ingredients"], $inserted_recipe["steps"])) {

        // Extract data from the HTTP input
        $recipe_name = mysqli_escape_string($database, $inserted_recipe["recipe_name"]);            
        $recipe_ingredients = $inserted_recipe["ingredients"];
        $recipe_steps = $inserted_recipe["steps"];

        // Check if the recipe name already exists in the DB
        $query_recipe_exists_in_DB = "SELECT recipe_name FROM recipes WHERE 1";                     
        $recipe_exists_in_DB = mysqli_query($database, $query_recipe_exists_in_DB);

        while($row = mysqli_fetch_assoc($recipe_exists_in_DB)) {
            if($recipe_name == $row["recipe_name"]) {
                http_response_code(409);	// Conflict
                echo "Error - recipe already exists in the database";
                return 0;
            }
        }
        
        // Create server files and Insert recipe info into the recipes table
        $recipe_path = "DB_data\\Recipes\\";;
        $recipe_folder_name = str_replace(' ', '_', $recipe_name);
        $recipe_steps_filename = "Steps_" . strtolower($recipe_folder_name) . ".json";
        
        $new_recipe_folder = $recipe_path . $recipe_folder_name;
        mkdir($new_recipe_folder);                                                      // Create new folder for the new recipe

        $new_recipe_file = $new_recipe_folder . "\\" . $recipe_steps_filename;
        $file = fopen($new_recipe_file, 'w+');                                          // Create JSON file with the new recipe's steps inside the new folder
        fwrite($file, json_encode($recipe_steps, JSON_PRETTY_PRINT));
	    fclose($file);

        $recipe_steps_filepath = addslashes($recipe_path . $recipe_folder_name . "\\" . $recipe_steps_filename);

        // Insert the new recipe data into the recipes table of the DB
        $query_add_recipe = "INSERT INTO recipes (recipe_name, recipe_steps) VALUES ('$recipe_name', '$recipe_steps_filepath')";        
        $add_recipe = mysqli_query($database, $query_add_recipe);
        
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

function delete_recipe($recipe_name) {
    global $database;
    
    $recipe_path = "DB_data\\Recipes\\" . $recipe_name;
    $recipe_steps_filepath = $recipe_path . "\\Steps_" . strtolower($recipe_name) . ".json";
    $recipe_name = str_replace('+', ' ', $recipe_name);

    $query_recipe_ID = "SELECT recipe_ID FROM recipes WHERE recipe_name = '$recipe_name'";
    $recipe_ID = mysqli_fetch_row(mysqli_query($database, $query_recipe_ID))[0];

    if($recipe_ID == false) {
        echo "Error, typo in input";
        http_response_code(400);    // Bad Request
        return 0;
    }

    // Delete recipe content in DB
    $query_delete_recipe = "DELETE FROM recipes WHERE recipe_ID = '$recipe_ID'";
    $delete_recipe = mysqli_query($database, $query_delete_recipe);

    if($delete_recipe !== false) {
		http_response_code(204);                // OK with no content
	}
	else {
		http_response_code(500);        // Internal Server Error
        return 0;
    }

    // Delete ingredient connections with deleted recipe
    $query_delete_connections = "DELETE FROM ingredients_for_recipes WHERE recipe_ID = '$recipe_ID'";
    if(mysqli_query($database, $query_delete_connections)) {
		http_response_code(204);            // OK with no content
	}
	else {
		http_response_code(500);            // Internal Server Error
        return 0;
    }

    // Delete the recipe's directory from the server's files
    unlink($recipe_steps_filepath);
    rmdir($recipe_path);
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
	
    // Get indices of the recipes containing received ingredient indices from the table of recipe-ingredient connections (create the sql query)
	$query_recipe_indices = "SELECT recipe_ID FROM ingredients_for_recipes WHERE ingredient_ID = ";			
	$first_recipe_index_found = false;
	while($row = mysqli_fetch_assoc($ingredient_indices)) {											
		$indeks_sestavine = $row["ingredient_ID"];
		if($first_recipe_index_found) {
			$query_recipe_indices = $query_recipe_indices . " OR ingredient_ID = $indeks_sestavine";
		}
	else {
			$query_recipe_indices = $query_recipe_indices . "$indeks_sestavine";
			$first_recipe_index_found = true;
		}
	}
	$query_recipe_indices = $query_recipe_indices . " GROUP BY recipe_ID HAVING COUNT(*) = $number_of_inserted_ingredients";
	$recipe_indices = mysqli_query($database, $query_recipe_indices);
   
    // Get relative path to JSON file with recipe steps from the recipes table (create the sql query)
	if($recipe_indices == false) {
        http_response_code(404);		// Not Found
		echo "Error - ingredients not found";
		return 0;
    }
    
    $query_recipe_steps_relative_path = "SELECT recipe_steps FROM recipes WHERE recipe_ID = ";			
	$first_recipe_found = false;
	while($row = mysqli_fetch_assoc($recipe_indices)) {
		$recipe_index = $row["recipe_ID"];
		if($first_recipe_found) {
			$query_recipe_steps_relative_path = $query_recipe_steps_relative_path . " OR recipe_ID = $recipe_index";
		}
		else {
			$query_recipe_steps_relative_path = $query_recipe_steps_relative_path . $recipe_index;
			$first_recipe_found = true;
		}
	}
	$recipe_steps_relative_path_sql = mysqli_query($database, $query_recipe_steps_relative_path);
	
	// Get recipe steps from the files, they'll be sent to the front end
	if ($recipe_steps_relative_path_sql == false) {														
		http_response_code(404);		// Not Found
		echo "Error - recipe steps not found";
		return 0;
	}
	$all_recipe_steps = "";

	while($row = mysqli_fetch_assoc($recipe_steps_relative_path_sql)) {
		$recipe_steps_path = $row["recipe_steps"];
		$recipe_steps = file_get_contents($recipe_steps_path);
		$all_recipe_steps = $all_recipe_steps . "\n" . $recipe_steps;
	}
	
	http_response_code(200);		//OK
	echo $all_recipe_steps;
}

function get_recipe($recipe_name) {

}

function edit_recipe($recipe_name) {

    global $database;
	
    $inserted_recipe = json_decode(file_get_contents('php://input'), true);
    $recipe_name = str_replace('+', ' ', $recipe_name);

    if(isset($inserted_recipe["new_recipe_name"], $inserted_recipe["ingredients"], $inserted_recipe["steps"])) {

        // Extract data from the HTTP input
        $new_recipe_name = mysqli_escape_string($database, $inserted_recipe["new_recipe_name"]);            
        $recipe_ingredients = $inserted_recipe["ingredients"];
        $recipe_steps = $inserted_recipe["steps"];

        // Check if the old recipe name exists in the DB
        $query_recipe_exists_in_DB = "SELECT recipe_name FROM recipes WHERE 1";             
        $recipe_exists_in_DB = mysqli_query($database, $query_recipe_exists_in_DB);

        $recipe_exists = false;
        while($row = mysqli_fetch_assoc($recipe_exists_in_DB)) {
            if($recipe_name == $row["recipe_name"]) {
                $recipe_exists = true;
            }
        }
        
        if(!$recipe_exists) {
            http_response_code(404);    // Not found
            echo "Error - recipe you wish to update does not exist!";
            return 0;
        }

        // Update server files and Insert recipe info into the recipes table
        $recipe_path = "DB_data\\Recipes\\";

        $old_recipe_folder_name = str_replace(' ', '_', $recipe_name);
        $new_recipe_folder_name = str_replace(' ', '_', $new_recipe_name);
        $old_recipe_steps_filename = "Steps_" . strtolower($old_recipe_folder_name) . ".json";
        $new_recipe_steps_filename = "Steps_" . strtolower($new_recipe_folder_name) . ".json";
        
        $old_recipe_folder = $recipe_path . $old_recipe_folder_name;
        $new_recipe_folder = $recipe_path . $new_recipe_folder_name;
        rename($old_recipe_folder, $new_recipe_folder);                                                      // Update folder name for the new recipe

        $old_recipe_file = $new_recipe_folder . "\\" . $old_recipe_steps_filename;
        $new_recipe_file = $new_recipe_folder . "\\" . $new_recipe_steps_filename;
        rename($old_recipe_file, $new_recipe_file);

        $file = fopen($new_recipe_file, 'w+');                                          // Update JSON file with the new recipe's steps inside the new folder
        fwrite($file, json_encode($recipe_steps, JSON_PRETTY_PRINT));
	    fclose($file);

        $new_recipe_steps_filepath = addslashes($recipe_path . $new_recipe_folder_name . "\\" . $new_recipe_steps_filename);

        $query_update_recipe = "UPDATE recipes SET recipe_name = '$new_recipe_name', recipe_steps = '$new_recipe_steps_filepath' WHERE recipe_name = '$recipe_name'";     // insert the new recipe data into the recipes table of the DB
        $update_recipe = mysqli_query($database, $query_update_recipe);
        
        // Insert new ingredients into ingredients table
        $number_of_ingredients = count($recipe_ingredients);
        for($ingredient_index = 1; $ingredient_index <= $number_of_ingredients; $ingredient_index++) {
            
            $ingredient_name = strtolower($recipe_ingredients["ingredient" . "$ingredient_index"]);

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
        $query_recipe_ID = "SELECT recipe_ID FROM recipes WHERE recipe_name = '$recipe_name'";
        $recipe_ID = mysqli_fetch_row(mysqli_query($database, $query_recipe_ID))[0];

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
            
            if($delete_connections !== false) {
                http_response_code(204);                // OK with no content
            }
            else {
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