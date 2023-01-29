<?php

function dbConnect()
{
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "auto_recipe";

	// Ustvarimo povezavo do podatkovne zbirke
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	mysqli_set_charset($conn,"utf8");
	
	// Preverimo uspeh povezave
	if (mysqli_connect_errno())
	{
		printf("Povezovanje s podatkovnim streznikom ni uspelo: %s\n", mysqli_connect_error());
		exit();
	} 	
	return $conn;
}

function change_data($database, $nickname, $data_type, $data) {
	$query_user_exists = "SELECT nickname FROM user_data WHERE nickname = '$nickname'";
	$user_exists = mysqli_query($database, $query_user_exists);
	
	if(mysqli_num_rows($user_exists) > 0)
	{	
		// If nickname is being changed, check if another user has the new nickname and change added_by column in the recipes table too
		if($data_type == "nickname") {
			$query_nickname_used = "SELECT nickname FROM user_data WHERE nickname = '$data'";
			$nickname_used = mysqli_query($database, $query_nickname_used);

			if(mysqli_num_rows($nickname_used) > 0) {
				http_response_code(409); // Conflict
				return 0;
			}

			$query_update_user_recipes = "UPDATE recipes SET added_by = '$data' WHERE added_by = '$nickname'";
			$update_user_recipes = mysqli_query($database, $query_update_user_recipes);
		}

		$query_update_user = "UPDATE user_data SET $data_type = '$data' WHERE nickname = '$nickname'";
		$update_user = mysqli_query($database, $query_update_user);
			
		if($update_user !== false)
		{
			http_response_code(201);	// Created
			//echo "User updated successfully";
		}
		else
		{
			http_response_code(500);	// Internal Server Error
		}
	}
	else
	{
		//echo "Error - User doesn't exist";
		http_response_code(409);	// Conflict
	}
}

?>