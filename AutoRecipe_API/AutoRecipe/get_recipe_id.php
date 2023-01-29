<?php
#####################################################################
# THIS SCRIPT IS TO BE USED WHEN EDITING A RECIPE'S NAME IN THE APP #
# When a user wishes to edit a recipe's name, the old name is first #
# used to get the recipe's ID in the database (primary key) and     #
# that information can be used to save the new recipe name properly #
#####################################################################

$DEBUG = true;  // Debug mode (return detailed error messages)

include("tools.php"); 

$database = dbConnect();	// Create a connection to the DB

header('Content-Type: application/json');	// Nastavimo MiME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)

$headers = apache_request_headers();

if(isset($headers['Authorization'])) {
	$headers = trim($headers["Authorization"]);
	if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        $token = $matches[1];
        $query_check_saved_token = "SELECT token FROM user_data WHERE token = '$token'";
        $check_saved_token = mysqli_query($database, $query_check_saved_token);
        if(mysqli_num_rows($check_saved_token) > 0) {
            if($_SERVER["REQUEST_METHOD"] == 'GET') {
                if(!empty($_GET["recipe_name"])) {
                    $recipe_name = str_replace("+", " ", $_GET["recipe_name"]);
                    
                    $query_recipe_id = "SELECT recipe_ID FROM recipes WHERE recipe_name = '$recipe_name'";
                    $recipe_id = mysqli_query($database, $query_recipe_id);

                    while($row = mysqli_fetch_assoc($recipe_id)) {
                        echo $row["recipe_ID"];
                    }
                }
                else {
                    http_response_code(400);	// Bad Request
                }
            }
            else {
                http_response_code(405);	// Method Not Allowed
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

?>