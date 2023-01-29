<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)

include("tools.php"); 					// Vključitev tools.php

$database = dbConnect();					// Pridobitev povezave s podatkovno zbirko

header('Content-Type: application/json');	// Nastavimo MiME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

switch($_SERVER["REQUEST_METHOD"])					// Glede na HTTP metodo v zahtevi izberemo ustrezno dejanje nad virom
{
    case 'POST':
		add_user_favorite();
		break;
    case 'GET':
        if(!empty($_GET["nickname"]))
		{
			get_user_favorites($_GET["nickname"]);
		}
		else
		{
			http_response_code(400);	// Bad Request
		}
    case 'DELETE':
		if(!empty($_GET["recipe_name"]))
		{
			delete_user_favorite($_GET["recipe_name"]);
		}
		else
		{
			http_response_code(400);	// Bad Request
		}
		break;  
	default:
		http_response_code(405);					//Če naredimo zahtevo s katero koli drugo metodo je to 'Method Not Allowed'
		break;
}

mysqli_close($database);							// Sprostimo povezavo z zbirko

// ----------- konec skripte, sledijo funkcije -----------

function add_user_favorite() {
    global $database;

    $inserted_favorite = json_decode(file_get_contents('php://input'), true);
    $nickname = $inserted_favorite["nickname"];
    $recipe_name = $inserted_favorite["recipe_name"];

    $query_favorite_exists = "SELECT * FROM user_favorites WHERE nickname = '$nickname' AND recipe_name = '$recipe_name'";
    $favorite_exists = mysqli_query($database, $query_favorite_exists);

    if(mysqli_num_rows($favorite_exists) > 0) {
        echo "Error - Favorite already exists";
        return 0;
    }

    $query_add_user_favorite = "INSERT INTO user_favorites (nickname, recipe_name) VALUES ('$nickname', '$recipe_name')";
    $add_user_favorite = mysqli_query($database, $query_add_user_favorite);

    if($add_user_favorite !== false) {
        http_response_code(201);	// Created
		echo "Favorite created successfully";
    }
    else {
        http_response_code(500);	// Internal Server Error
    }
}

function get_user_favorites($nickname) {
    global $database;

    $query_get_user_favorites = "SELECT recipe_name FROM user_favorites WHERE nickname = '$nickname'";
    $get_user_favorites = mysqli_query($database, $query_get_user_favorites);

    if($get_user_favorites == false) {
        echo "Error - no user favorites";
        return 0;
    }

    while($row = mysqli_fetch_assoc($get_user_favorites)) {
        $user_favorites[] = $row;
    }

    echo json_encode($user_favorites);
}

?>