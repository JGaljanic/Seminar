<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)

include("tools.php"); 					// Vključitev tools.php

$zbirka = dbConnect();					// Pridobitev povezave s podatkovno zbirko

header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

$vstavljene_sestavine = "glass";

switch($_SERVER["REQUEST_METHOD"])					// Glede na HTTP metodo v zahtevi izberemo ustrezno dejanje nad virom
{
	case 'POST':
		poslji_izbrane_sestavine();					// Shrani vstavljene sestavine v json formatu na strezniku
		break;
	case 'GET':
		pridobi_recepte($vstavljene_sestavine);		// Vrni recepte glede na vstavljene sestavine
		break;
	default:
		http_response_code(405);					//Če naredimo zahtevo s katero koli drugo metodo je to 'Method Not Allowed'
		break;
}

mysqli_close($zbirka);							// Sprostimo povezavo z zbirko


// ----------- konec skripte, sledijo funkcije -----------


function poslji_izbrane_sestavine() {
	global $zbirka;
	$vstavljene_sestavine = file_get_contents('php://input');
	
	$file = fopen('vstavljene_sestavine.json', 'w+');
	fwrite($file, $vstavljene_sestavine);
	fclose($file);
	
	if(file_exists('vstavljene_sestavine.json')) {
		
		http_response_code(201);	// Created
		echo "File created";
	}
	else {
		http_response_code(500);	// Internal server error
		echo "Napaka";
	}
}

function pridobi_recepte($vstavljene_sestavine) {
	
	global $zbirka;
	
	$absolutna_pot = "D:\\Tools\\xampp\\htdocs\\";
	
	$poizvedba_indeks_sestavine = "SELECT ingredient_ID FROM ingredients WHERE ingredient_name = '$vstavljene_sestavine'";		// Pridobi indekse sestavin, pozneje verjetno ne bo potrebno ker bo front end poslal indekse
	$indeks_sestavine = mysqli_fetch_array(mysqli_query($zbirka, $poizvedba_indeks_sestavine))[0];
	
	$poizvedba_indeksi_receptov = "SELECT recipe_ID FROM ingredients_for_recipes WHERE ingredient_ID = $indeks_sestavine";
	$indeksi_receptov = mysqli_query($zbirka, $poizvedba_indeksi_receptov);
	
	if ($indeksi_receptov == false) {
		http_response_code(404);
		echo "!!!!!!!!!!!!!!!!!!!!Typo v vstavljenih sestavinah!!!!!!!!!!!!!!!!!!!!";
		return 0;
	}
	else {
	$poizvedba_koraki_receptov_relativna_pot = "SELECT recipe_steps FROM recipes WHERE recipe_ID = ";
	$prvi_recept_najden = false;
	while($vrstica = mysqli_fetch_assoc($indeksi_receptov)) {
		$indeks_recepta = $vrstica["recipe_ID"];
		if($prvi_recept_najden !== false) {
			$poizvedba_koraki_receptov_relativna_pot = $poizvedba_koraki_receptov_relativna_pot . " OR recipe_ID = $indeks_recepta";
		}
		else {
			$poizvedba_koraki_receptov_relativna_pot = $poizvedba_koraki_receptov_relativna_pot . $indeks_recepta;
			$prvi_recept_najden = true;
		}
	}
	
	$koraki_receptov_relativna_pot_sql = mysqli_query($zbirka, $poizvedba_koraki_receptov_relativna_pot);
	
	$koraki_vseh_receptov = "";
	
	while($vrstica = mysqli_fetch_assoc($koraki_receptov_relativna_pot_sql)) {
		$koraki_recepta_relativna_pot = $vrstica["recipe_steps"];
		$koraki_recepta_pot = $absolutna_pot . $koraki_recepta_relativna_pot;
		$koraki_recepta = file_get_contents($koraki_recepta_pot);
		$koraki_vseh_receptov = $koraki_vseh_receptov . "\n" . $koraki_recepta;
	}
	
	http_response_code(200);		//OK
	echo $koraki_vseh_receptov;
	
	}
}

?>