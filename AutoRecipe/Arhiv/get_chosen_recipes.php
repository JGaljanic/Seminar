<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)

include("tools.php"); 					// Vključitev tools.php

$database = dbConnect();					// Pridobitev povezave s podatkovno zbirko

header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

switch($_SERVER["REQUEST_METHOD"])					// Glede na HTTP metodo v zahtevi izberemo ustrezno dejanje nad virom
{
	case 'POST':
		send_chosen_ingredients();					// Shrani vstavljene sestavine v json formatu na strezniku
		break;
	case 'GET':
		get_recipes();							// Vrni recepte glede na vstavljene sestavine
		break;
	default:
		http_response_code(405);					//Če naredimo zahtevo s katero koli drugo metodo je to 'Method Not Allowed'
		break;
}

mysqli_close($database);							// Sprostimo povezavo z zbirko


// ----------- konec skripte, sledijo funkcije -----------


function send_chosen_ingredients() {
	global $database;
	$inserted_ingredients = file_get_contents('php://input');
	
	$file = fopen('inserted_ingredients.json', 'w+');
	fwrite($file, $inserted_ingredients);
	fclose($file);
	
	if(file_exists('inserted_ingredients.json')) {
		
		http_response_code(201);	// Created
		echo "File created";
	}
	else {
		http_response_code(500);	// Internal server error
		echo "Error";
	}
}

function get_recipes() {
	
	global $database;
	
	$absolute_path = "D:\\Tools\\xampp\\htdocs\\";
	
	$inserted_ingredients = array_unique(json_decode(file_get_contents("inserted_ingredients.json"), true));
	$query_ingredient_indices = "SELECT ingredient_ID FROM ingredients WHERE ingredient_name = ";				// Get ingredients indices
	$inserted_ingredient_index = 1;
	$number_of_ingredients = count($inserted_ingredients);

	while($inserted_ingredient_index <= $number_of_ingredients) {									
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

	
	$query_recipe_indices = "SELECT recipe_ID FROM ingredients_for_recipes WHERE ingredient_ID = ";			// Get recipe indicies
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
	$query_recipe_indices = $query_recipe_indices . " GROUP BY recipe_ID HAVING COUNT(*) = $number_of_ingredients";
	$recipe_indices = mysqli_query($database, $query_recipe_indices);


	$query_recipe_steps_relative_path = "SELECT recipe_steps FROM recipes WHERE recipe_ID = ";			// Get relative path to JSON file with recipe steps
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
	
	
	if ($recipe_steps_relative_path_sql == false) {														// Get recipe steps from the files, they'll be sent to the front end
		http_response_code(404);		// Not Found
		echo "Not Found";
		unlink("inserted_ingredients.json");
		return 0;
	}
	else {
		$all_recipe_steps = "";
	
		while($row = mysqli_fetch_assoc($recipe_steps_relative_path_sql)) {
			$recipe_steps_relative_path = $row["recipe_steps"];
			$recipe_steps_path = $absolute_path . $recipe_steps_relative_path;
			$recipe_steps = file_get_contents($recipe_steps_path);
			$all_recipe_steps = $all_recipe_steps . "\n" . $recipe_steps;
		}
	
		http_response_code(200);		//OK
		echo $all_recipe_steps;
		unlink("inserted_ingredients.json");
	}
}

?>