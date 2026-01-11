<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once 'db_connection.php';
use DB\DBConnection;

$html_page = file_get_contents("../pages/login.html");

// Variabili per placeholder
$form_errors = "";

// In caso di errori ripristiniamo gli input corretti
function input_restore() {
	$html_page = file_get_contents("../pages/login.html");
	$html_page = str_replace("[username]", htmlspecialchars(isset($_POST['username']) ? $_POST['username'] : ''), $html_page);
	$html_page = str_replace("[password]", htmlspecialchars(isset($_POST['password']) ? $_POST['password'] : ''), $html_page);
	return $html_page;
}
    
// Se esiste sessione login redirect automatico
session_start();
if (isset($_SESSION["user"])) {
    header("location: area_utente.php");
	exit();
}

// L'utente ha compilato tutti i campi del form
if (isset($_POST["username"]) && isset($_POST["password"])) {
    // Se qualche campo non è compilato
    if (empty($_POST["username"]) || empty($_POST["password"])) {
		$form_errors = $form_errors . "<p>Devi compilare tutti i campi.</p>";
        $html_page = input_restore();
		echo str_replace("[err]", $err, $html_page);
		exit();
	} else {
        try {
            // Transaction per il login dell'utente
            $db_connection = new DBConnection();
            $result = $db_connection->login_user($_POST["username"], $_POST["password"]);
            $db_connection->close_connection();

            // Utente non trovato
            if ($result == -1) {
                $form_errors = $form_errors . "<p>Lo <span lang='en'>username</span> che hai inserito non &egrave; stato trovato.</p>";
                $html_page = input_restore();
                echo str_replace("[err]", $form_errors, $html_page);
            } else {
                // TODO: Mettere controllo password
                // TODO: Mettere casistica per utente admin
                $_SESSION["user"] = $_POST["username"];
                header("location: area_utente.php");
            }
        } catch (Exception) {
            header("location: ../pages/500.html");
            exit();
        }
    }
} else {
    $html_page = input_restore();
    echo str_replace("[err]", $form_errors, $html_page);
}
?>