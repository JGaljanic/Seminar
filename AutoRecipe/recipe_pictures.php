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
            if ($_SERVER["REQUEST_METHOD"] == 'DELETE' && !empty($_GET["picture_path"])) {
                $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
                $picture_path = str_replace("/", "\\", $_GET["picture_path"]);
                $filepath = dirname("$rootDir\\AutoRecipe\\$picture_path") . "\\";
                $picture_name = basename($picture_path);
                $all_pictures = array_diff(scandir(dirname("$rootDir\\AutoRecipe\\$picture_path")),  array('..', '.'));
                $deleted_picture_index = array_search($picture_name, $all_pictures);

                unset($all_pictures[$deleted_picture_index]);

                for($i = $deleted_picture_index; $i <= count($all_pictures) + 1; $i++) {
                    $all_pictures[$i] = $all_pictures[$i + 1];
                    unset($all_pictures[$i + 1]);
                }
                
                unlink("$rootDir\\AutoRecipe\\$picture_path");

                for($i = 2; $i <= count($all_pictures); $i++) {
                    rename($filepath . $all_pictures[$i], $filepath . "step" . $i - 1 . ".jpg");
                }

                http_response_code(200); // OK
            }
            elseif($_SERVER["REQUEST_METHOD"] == 'POST' && !empty($_GET["picture_path"])) {
                $file = isset($_FILES['file']) ? $_FILES['file'] : NULL;
                $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);

                $tmpfile = $file['tmp_name'];

                $picture_path = str_replace("/", "\\", $_GET["picture_path"]);
                $filename = basename($picture_path);

                $saveto = "$rootDir\\AutoRecipe\\$picture_path";

                move_uploaded_file($tmpfile, $saveto);

                print 'File saved to: ' . $saveto;
                http_response_code(200);    // OK
                }
            else {
                http_response_code(405); // Method not allowed
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