<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)

include("tools.php"); 					// Vključitev tools.php

$zbirka = dbConnect();					// Pridobitev povezave s podatkovno zbirko

header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

$vstavljene_sestavine = "water";

switch($_SERVER["REQUEST_METHOD"])					// Glede na HTTP metodo v zahtevi izberemo ustrezno dejanje nad virom
{
	case 'GET':
		pridobi_recepte($vstavljene_sestavine);		// Vrni recepte glede na vstavljene sestavine
	default:
		http_response_code(405);					//Če naredimo zahtevo s katero koli drugo metodo je to 'Method Not Allowed'
		break;
}

mysqli_close($zbirka);							// Sprostimo povezavo z zbirko


// ----------- konec skripte, sledijo funkcije -----------


function pridobi_recepte($vstavljene_sestavine)
{
	global $zbirka;
	$vstavljene_sestavine = mysqli_escape_string($zbirka, $vstavljene_sestavine);
	
	$poizvedba_izbranih_receptov = "SELECT recipe_name, recipe_steps, ingredients FROM recipes WHERE recipe_ID = 1";
	
	$izbrani_recepti = mysqli_query($zbirka, $poizvedba_izbranih_receptov);
	
	$odgovor=mysqli_fetch_assoc($izbrani_recepti);
		
	http_response_code(200);		//OK
	echo json_encode($odgovor);

	/* if(mysqli_num_rows($izbrani_recepti)>0)	//recepti obstajajo
	{
		$odgovor=mysqli_fetch_assoc($izbrani_recepti);
		
		http_response_code(200);		//OK
		echo json_encode($odgovor);
	}
	else							// V podatkovni zbirki ni receptov z vstavljenimi sestavinami
	{
		http_response_code(404);		//Not found
	} */
}

?>