<?php
$DEBUG = true;							// Priprava podrobnejših opisov napak (med testiranjem)

include("tools.php"); 					// Vključitev tools.php

$zbirka = dbConnect();					// Pridobitev povezave s podatkovno zbirko

header('Content-Type: application/json');	// Nastavimo MIME tip vsebine odgovora
header('Access-Control-Allow-Origin: *');	// Dovolimo dostop izven trenutne domene (CORS)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');		//v preflight poizvedbi za CORS sta dovoljeni le metodi GET in POST

$vstavljene_sestavine = "milk";

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
	
	$absolutna_pot = "D:\\Tools\\xampp\\htdocs\\";
	$poizvedba_izbranih_receptov = "SELECT recipe_name, recipe_steps, ingredients FROM recipes WHERE recipe_ID = ";
	
	$dolzina_baze_poizvedba = "SELECT COUNT(*) FROM recipes";
	$dolzina_baze = mysqli_fetch_array(mysqli_query($zbirka, $dolzina_baze_poizvedba))[0];				// Konvertanje sql_result v string (prvi element arraya)
	
	$prvi_najden_recept = false;
	for ($i = 1; $i <= $dolzina_baze; $i++) {
		$poizvedba_relativna_pot = "SELECT ingredients FROM recipes WHERE recipe_ID = $i";				// Najdi relativno pot v zbirki podatkov za vsako vrstico
		$relativna_pot = mysqli_fetch_array(mysqli_query($zbirka, $poizvedba_relativna_pot))[0];
		$celotna_pot = $absolutna_pot . $relativna_pot;
		
		$sestavine_recepta = file_get_contents($celotna_pot);		
		
		if(strpos($sestavine_recepta, $vstavljene_sestavine) !== false) {								// Preveri če JSON sestavin iz zbirke podatkov vsebuje sestavino
			$ID_recepta = strval($i);
			
			if ($prvi_najden_recept) {
				$poizvedba_izbranih_receptov = $poizvedba_izbranih_receptov . " OR recipe_ID = " . $ID_recepta;
			}
			else {
				$poizvedba_izbranih_receptov = $poizvedba_izbranih_receptov . $ID_recepta;
				$prvi_najden_recept = true;
			}
		}
	}
	
	$izbrani_recepti = mysqli_query($zbirka, $poizvedba_izbranih_receptov);
	

	 if($izbrani_recepti !== false)		// Recepti obstajajo
	{
		while($recept = mysqli_fetch_assoc($izbrani_recepti)) {
			$odgovor[] = $recept;
		}
		http_response_code(200);		//OK
		echo json_encode($odgovor);
	}
	else								// V podatkovni zbirki ni receptov z vstavljenimi sestavinami
	{
		http_response_code(404);		//Not found
	}
}

?>