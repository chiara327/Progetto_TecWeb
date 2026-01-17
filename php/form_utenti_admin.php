<?php
require_once 'db_connection.php';
use DB\DBConnection;

$html_page = file_get_contents('../pages/form_utenti_admin.html');

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
	if (!preg_match("/^[A-Za-zÀ-ÿ\s\-\']+$/", $nome)) {
		$form_errors = $form_errors . "<p>Il nome deve contenere solo lettere, spazi, trattini e apostrofi.</p>";
	}

	// Caratteri ammessi: lettere (M e m), caratteri speciali, spazi, trattini e apostrofi
	if (!preg_match("/^[A-Za-zÀ-ÿ\s\-\']+$/", $cognome)) {
		$form_errors = $form_errors . "<p>Il cognome deve contenere solo lettere, spazi, trattini e apostrofi.</p>";
	}

	// Data non deve essere nel futuro e nel formato internazionale
	if (strtotime($_POST["data"]) > strtotime(date("Y-m-d")) || !preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/", $data)) {
		$form_errors = $form_errors . "<p>La data inserita non è valida, scegliere una data nel formato dd-mm-yyyy e che non sia nel futuro.</p>";
	}

	// Ammessi tutti i caratteri, controlliamo solo la lunghezza
	if (strlen($username) > 30) {
		$form_errors = $form_errors . "<p>Lo <span lang='en'>username</span> non deve superare i 30 caratteri.</p>";
	}

	// TODO: COME VOGLIAMO MOSTRARE L'ERRORE IN BASE A COSA MANCA NELLA PASSWORD? DIVERSI <P>? DIVERSI CONTROLLI CON APPEND DI DIVERSI <P>?
	// La password deve avere almeno 8 caratteri, contenere una lettera minuscola, una maiuscola, un numero e un carattere speciale
	if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
		$form_errors = $form_errors . "<p>La <span lang='en'>password</span> deve essere lunga almeno 8 caratteri e contenere: 1 lettera minuscola, 1 lettera maiuscola, 1 numero e 1 carattere speciale.</p>";
	}
}



echo $html_page;
?>