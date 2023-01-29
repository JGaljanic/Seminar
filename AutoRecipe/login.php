<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)

include("tools.php"); 					// Vključitev tools.php

$database = dbConnect();					// Pridobitev povezave s podatkovno zbirko

header('Content-Type: application/json');	// Nastavimo MiME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $login_data = json_decode(file_get_contents('php://input'), true);
    $login_nickname = $login_data["nickname"];
    $login_password = $login_data["password"];

    $query_user_exists = "SELECT * FROM user_data WHERE nickname = '$login_nickname'";
    $user_exists = mysqli_query($database, $query_user_exists);

    if(mysqli_num_rows($user_exists) > 0) {
        $query_password_correct = "SELECT * FROM user_data WHERE password = '$login_password' AND nickname = '$login_nickname'";
        $password_correct = mysqli_query($database, $query_password_correct);

        if(mysqli_num_rows($password_correct) > 0) {
            $token = hash("md5",$login_nickname.$login_password);
            
            $query_add_token = "UPDATE user_data SET token = '$token' WHERE nickname = '$login_nickname'";
            mysqli_query($database, $query_add_token);

			echo json_encode(array('token'=>$token));
            http_response_code(200);		//OK
        }
        else {
            http_response_code(409);	// Conflict
            return 0;
        }
    }
    else {
        http_response_code(404);		//Not found
        return 0;
    }
}
else {
    http_response_code(405);					//Če naredimo zahtevo s katero koli drugo metodo je to 'Method Not Allowed'
	return 0;
}

mysqli_close($database);							// Sprostimo povezavo z zbirko

?>