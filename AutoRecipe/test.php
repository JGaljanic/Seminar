<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)

include("tools.php"); 					// Vključitev tools.php

$database = dbConnect();					// Pridobitev povezave s podatkovno zbirko

header('Content-Type: application/json');	// Nastavimo MiME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

/*$query = "SELECT connection_ID FROM ingredients_for_recipes WHERE recipe_ID = 3 AND ingredient_ID NOT IN (3, 4)";
$result = mysqli_query($database, $query);

var_dump(mysqli_fetch_row($result));*/

file_get_contents("AutoRecipe\DB_data\Recipes\Glass_of_Coca-Cola\Steps_glass_of_Coca-Cola.json");

mysqli_close($database);							// Sprostimo povezavo z zbirko
?>