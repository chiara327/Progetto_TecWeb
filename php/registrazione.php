<?php
require_once 'db_connection.php';
use DB\DBConnection;

$html_page = file_get_contents("../pages/registrazione.html");

// Variabili per placeholder
$form_errors = "";

// In caso di errori ripristiniamo gli input corretti
function input_restore() {
	$html_page = file_get_contents("../pages/registrazione.html");
	$html_page = str_replace("[nome]", htmlspecialchars(isset($_POST['nome']) ? $_POST['nome'] : ''), $html_page);
	$html_page = str_replace("[cognome]", htmlspecialchars(isset($_POST['cognome']) ? $_POST['cognome'] : ''), $html_page);
	$html_page = str_replace("[username]", htmlspecialchars(isset($_POST['username']) ? $_POST['username'] : ''), $html_page);
	$html_page = str_replace("[password]", htmlspecialchars(isset($_POST['password']) ? $_POST['password'] : ''), $html_page);
	$html_page = str_replace("[data]", htmlspecialchars(isset($_POST['data']) ? $_POST['data'] : ''), $html_page);
	return $html_page;
}

function check_invalid_input($nome, $cognome, $data, $username, $password) {
	global $form_errors;
	// Caratteri ammessi: lettere (M e m), caratteri speciali, spazi, trattini e apostrofi
	if (!preg_match("/^[A-Za-zÀ-ÿ\s\-\']+$/", $_POST["nome"])) {
		$form_errors = $form_errors . "<p>Il nome deve contenere solo lettere, spazi, trattini e apostrofi.</p>";
	}

	// Caratteri ammessi: lettere (M e m), caratteri speciali, spazi, trattini e apostrofi
	if (!preg_match("/^[A-Za-zÀ-ÿ\s\-\']+$/", $_POST["cognome"])) {
		$form_errors = $form_errors . "<p>Il cognome deve contenere solo lettere, spazi, trattini e apostrofi.</p>";
	}

	// Data non deve essere nel futuro e nel formato internazionale
	if (strtotime($_POST["data"]) > strtotime(date("Y-m-d")) || !preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/", $_POST["data"])) {
		$form_errors = $form_errors . "<p>La data inserita non è valida, scegliere una data nel formato dd-mm-yyyy e che non sia nel futuro.</p>";
	}

	// Ammessi tutti i caratteri, controlliamo solo la lunghezza
	if (strlen($_POST["username"]) > 30) {
		$form_errors = $form_errors . "<p>Lo <span lang='en'>username</span> non deve superare i 30 caratteri.</p>";
	}

	// TODO: COME VOGLIAMO MOSTRARE L'ERRORE IN BASE A COSA MANCA NELLA PASSWORD? DIVERSI <P>? DIVERSI CONTROLLI CON APPEND DI DIVERSI <P>?
	// La password deve avere almeno 8 caratteri, contenere una lettera minuscola, una maiuscola, un numero e un carattere speciale
	if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $_POST["password"])) {
		$form_errors = $form_errors . "<p>La <span lang='en'>password</span> deve essere lunga almeno 8 caratteri e contenere: 1 lettera minuscola, 1 lettera maiuscola, 1 numero e 1 carattere speciale.</p>";
	}
}

// Se esiste sessione login redirect automatico
session_start();
if (isset($_SESSION["user"])) {
	// Se abbiamo voglia/ci viene imposto, fare pagina home per utente
    // loggato in cui puoi fare diverse azioni
    //header("location: utente.php");
	exit();
}

// L'utente ha compilato tutti i campi del form
if (isset($_POST["nome"]) && isset($_POST["cognome"]) && isset($_POST["data"]) && isset($_POST["username"]) && isset($_POST["password"])) { 
	// Se qualche campo non è compilato
	if (empty($_POST["nome"]) || empty($_POST["cognome"]) || empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["data"])) {
		$form_errors = $form_errors . "<p>Devi compilare tutti i campi.</p>";
		$html_page = input_restore();
		echo str_replace("[err]", $form_errors, $html_page);
		exit();
	} else {
		// Validazione degli input, se riscontra errori li segnala in $form_errors
		check_invalid_input($_POST["nome"], $_POST["cognome"], $_POST["data"], $_POST["username"], $_POST["password"]);
		// Ha segnalato errori negli input
		if (!empty($form_errors)) {
			$html_page = input_restore();
			echo str_replace("[err]", $form_errors, $html_page);
			exit();
		}

		// Transaction per registrare nuovo utente
		try {
			$db_connection = new DBConnection();
			$result = $db_connection->register_new_user($_POST["username"], $_POST["password"], $_POST["nome"], $_POST["cognome"], $_POST["data"]);
			$db_connection->close_connection();

			// Username esistente
			if ($result == -1) {
				$form_errors = $form_errors . "<p>Lo <span lang='en'>username</span> che hai scelto &egrave; stato già registrato.</p>";
				$html_page = input_restore();
				echo str_replace("[err]", $form_errors, $html_page);
				exit();
			} else {
				$_SESSION["user"] = $_POST["username"];
				header("location: ../pages/area_utente.html");
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