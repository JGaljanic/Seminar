<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)

include("tools.php"); 					// Vključitev tools.php

$database = dbConnect();					// Pridobitev povezave s podatkovno zbirko

header('Content-Type: application/json');	// Nastavimo MiME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

$nickname = $_GET["nickname"];
$query_password = "SELECT password FROM user_data WHERE nickname = '$nickname'";
$password = mysqli_fetch_assoc(mysqli_query($database, $query_password));
$password = $password["password"];

$headers = apache_request_headers();

// If a new user is created no authentication is required, otherwise it is
if($_SERVER["REQUEST_METHOD"] == 'POST') add_user();
else {
// Authentication for the rest of the user data operations
if(isset($headers['Authorization'])) {
	$headers = trim($headers["Authorization"]);
	if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
		if($matches[1] == hash("md5", $nickname.$password)){
			switch($_SERVER["REQUEST_METHOD"])
			{
				case 'PUT':
					if(!empty($_GET["nickname"]))
					{
						update_user($_GET["nickname"]);
					}
					else
					{
						http_response_code(400);	// Bad Request
					}
					break;
				case 'DELETE':
					if(!empty($_GET["nickname"]))
					{
						delete_user($_GET["nickname"]);
					}
					else
					{
						http_response_code(400);	// Bad Request
					}
					break;
				case 'GET':
					if(!empty($_GET["nickname"]))
					{
						get_user_data($_GET["nickname"]);
					}
					else
					{
						http_response_code(400);	// Bad Request
					}
					break;
				default:
					http_response_code(405);
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
}

mysqli_close($database);							// Sprostimo povezavo z zbirko

// ----------- konec skripte, sledijo funkcije -----------

function add_user() {
    global $database;

    $user_data = json_decode(file_get_contents('php://input'), true);
	
	if(isset($user_data["nickname"], $user_data["password"], $user_data["email"]))
	{
		$nickname = mysqli_escape_string($database, $user_data["nickname"]);
		$password = mysqli_escape_string($database, $user_data["password"]);
		$email = mysqli_escape_string($database, $user_data["email"]);

        $query_user_exists = "SELECT nickname FROM user_data WHERE nickname = '$nickname'";
        $user_exists = mysqli_query($database, $query_user_exists);
			
		if(mysqli_num_rows($user_exists) == 0)
		{	
			$query_add_user = "INSERT INTO user_data (nickname, password, email) VALUES ('$nickname', '$password', '$email')";
            $add_user = mysqli_query($database, $query_add_user);
			
			if($add_user !== false)
			{
				http_response_code(201);	// Created
				echo "User created successfully";
			}
			else
			{
				http_response_code(500);	// Internal Server Error
			}
		}
		else
		{
            echo "Error - User already exists";
			http_response_code(409);	// Conflict
		}
	}
	else
	{
		http_response_code(400);	// Bad Request
	}
}

function update_user($nickname) {
    global $database;
	global $password;
	global $nickname;

    $user_data = json_decode(file_get_contents('php://input'), true);

	switch($user_data) {
		case isset($user_data["nickname"]):
			// Change nickname
			$new_nickname = mysqli_escape_string($database, $user_data["nickname"]);
			change_data($database, $nickname, "nickname", $new_nickname);
			$token = hash("md5",$new_nickname.$password);
			$query_change_token = "UPDATE user_data SET token = '$token' WHERE nickname = '$new_nickname' AND password = '$password'";
            mysqli_query($database, $query_change_token);

			echo json_encode(array('token'=>$token));
			break;
		case isset($user_data["password"]):
			// Change password
			$new_password = mysqli_escape_string($database, $user_data["password"]);
			change_data($database, $nickname, "password", $new_password);
			$token = hash("md5",$nickname.$new_password);
			$query_change_token = "UPDATE user_data SET token = '$token' WHERE password = '$new_password' AND nickname = '$nickname'";
            mysqli_query($database, $query_change_token);

			echo json_encode(array('token'=>$token));
			break;
		case isset($user_data["email"]):
			// Change email
			$new_email = mysqli_escape_string($database, $user_data["email"]);
			change_data($database, $nickname, "email", $new_email);
			break;
		default:
			http_response_code(400);	// Bad request
			break;
	}
}

function delete_user($nickname) {
	global $database;

	$query_delete_user = "DELETE FROM user_data WHERE nickname = '$nickname'";
	$delete_user = mysqli_query($database, $query_delete_user);

	if($delete_user !== false) {
		http_response_code(204);	//OK with no content
	}
	else {
		http_response_code(500);	// Internal Server Error
	}
}

function get_user_data($nickname) {
	global $database;

	$query_get_user_data = "SELECT nickname, password, email FROM user_data WHERE nickname = '$nickname'";
	$get_user_data = mysqli_query($database, $query_get_user_data);

	if(mysqli_num_rows($get_user_data) > 0)
	{
		$user_data = mysqli_fetch_assoc($get_user_data);
		
		http_response_code(200);		//OK
		echo json_encode($user_data);
	}
	else
	{
		http_response_code(404);		//Not found
	}
}
?>