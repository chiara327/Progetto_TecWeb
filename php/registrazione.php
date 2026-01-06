<?php
require_once 'db_connection.php';
use DB\DBConnection;

function input_restore() {
	$html_page = file_get_contents("../pages/registrazione.html");
	$html_page = str_replace("[nome]", htmlspecialchars(isset($_POST['nome']) ? $_POST['nome'] : ''), $html_page);
	$html_page = str_replace("[cognome]", htmlspecialchars(isset($_POST['cognome']) ? $_POST['cognome'] : ''), $html_page);
	$html_page = str_replace("[username]", htmlspecialchars(isset($_POST['username']) ? $_POST['username'] : ''), $html_page);
	$html_page = str_replace("[password]", htmlspecialchars(isset($_POST['password']) ? $_POST['password'] : ''), $html_page);
	$html_page = str_replace("[data]", htmlspecialchars(isset($_POST['data']) ? $_POST['data'] : ''), $html_page);
	return $html_page;
}

$html_page = file_get_contents("../pages/registrazione.html");

// Variabili per placeholder
$form_errors = "";

// Se esiste sessione login redirect automatico
session_start();
if (isset($_SESSION["utente"])) {
	// Se abbiamo voglia/ci viene imposto, fare pagina home per utente
    // loggato in cui puoi fare diverse azioni
    //header("location: utente.php");
	exit();
}

// TODO: TEST if(isset($_POST["nome"]) && isset($_POST["cognome"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["data"])) {
if (isset($_POST["username"]) && isset($_POST["password"])) { 
	$form_errors = $form_errors . '<p>LETSGOSKI</p>';
	echo str_replace("[err]", $form_errors, $html_page);
} else {
    $html_page = input_restore();
	echo str_replace("[err]", $form_errors, $html_page);
}

?>