<?php

$DEBUG = true;

include("tools.php");

$database = dbConnect();    // Connect to the database using the function in tools.php

header('Content-Type: text/plain');	// Nastavimo MiME tip vsebine odgovora
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
                
                $recipe_ID = $_GET["recipe_ID"];

                $query_get_thumbnail = "SELECT recipe_thumbnail FROM recipes WHERE recipe_ID = '$recipe_ID'";
                $get_thumbnail = mysqli_query($database, $query_get_thumbnail);

                if($get_thumbnail != false) {
                    http_response_code(200);    // OK

                    while($row = mysqli_fetch_assoc($get_thumbnail)) {
                        $thumbnail[] = $row;
                    }

                    echo $rootDir = $thumbnail[0]["recipe_thumbnail"];
                }
                else {
                    http_response_code(404);    // ID not found
                    echo "ID was not found in the database";
                    return 0;
                }
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

?>