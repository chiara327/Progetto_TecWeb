<?php
require_once 'db_connection.php';
use DB\DBConnection;

$html_page = file_get_contents('../pages/form_utenti_admin.html');
$form_errors = "";

function input_restore() {
	$html_page = file_get_contents("../pages/form_utenti_admin.html");
	$html_page = str_replace("[nome]", htmlspecialchars(isset($_POST['nome']) ? $_POST['nome'] : ''), $html_page);
	$html_page = str_replace("[cognome]", htmlspecialchars(isset($_POST['cognome']) ? $_POST['cognome'] : ''), $html_page);
	$html_page = str_replace("[username]", htmlspecialchars(isset($_POST['username']) ? $_POST['username'] : ''), $html_page);
	$html_page = str_replace("[password]", htmlspecialchars(isset($_POST['password']) ? $_POST['password'] : ''), $html_page);
	$html_page = str_replace("[data]", htmlspecialchars(isset($_POST['data']) ? $_POST['data'] : ''), $html_page);
	return $html_page;
}

// TODO: FARE COME NEGLI ALTRI FORM CON HEADER SU SELF_PHP
// TODO: NON FUNZIONA ELIMINA UTENTE, GLI ERRORI IN PLACEHOLDER NON VANNO
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

if (isset($_POST["creazione_utente"])) {
    if (empty($_POST["nome"]) || empty($_POST["cognome"]) || !isset($_POST["adminPower"]) || empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["data"])) {
		$form_errors = $form_errors . "<p>Devi compilare tutti i campi.</p>";
		$html_page = input_restore();
		echo str_replace(["[err_utenti_creazione]", "[err_utenti_elimina]"], [$form_errors, ""], $html_page);
		exit();
	} else {
		// Validazione degli input, se riscontra errori li segnala in $form_errors
		check_invalid_input($_POST["nome"], $_POST["cognome"], $_POST["data"], $_POST["username"], $_POST["password"]);
		// Ha segnalato errori negli input
		if (!empty($form_errors)) {
			$html_page = input_restore();
			echo str_replace(["[err_utenti_creazione]", "[err_utenti_elimina]"], [$form_errors, ""], $html_page);
			exit();
		}

		// Transaction per registrare nuovo utente
		try {
			$db_connection = new DBConnection();
			$result = $db_connection->register_new_user($_POST["username"], password_hash($_POST["password"], PASSWORD_BCRYPT), $_POST["nome"], $_POST["cognome"], $_POST["data"], $_POST["adminPower"]);
			$db_connection->close_connection();

			// Username esistente
			if (!$result) {
				$form_errors = $form_errors . "<p>Lo <span lang='en'>username</span> che hai scelto &egrave; stato già registrato.</p>";
				$html_page = input_restore();
				echo str_replace(["[err_utenti_creazione]", "[err_utenti_elimina]"], [$form_errors, ""], $html_page);
				exit();
			} else {
				header("location: area_amministratore.php");
			}
		} catch (Exception) {
			header("location: ../pages/500.html");
			exit();
		}
	}
    echo str_replace(["[err_utenti_creazione]", "[err_utenti_elimina]"], [$form_errors, ""], $html_page);
	
    exit();
} else if (isset($_POST["elimina_utente"])) {
    if (empty($_POST["username_delete"])) {
        $form_errors = "<p>Devi inserire uno username da eliminare.</p>";
        $html_page = input_restore();
        echo str_replace(["[err_utenti_creazione]", "[err_utenti_elimina]"], ["", $form_errors], $html_page);
        exit();
    }
    try {
        $db_connection = new DBConnection();
        $userExists = $db_connection->check_for_existing_username($_POST["username_delete"]);
        if ($userExists) {
            $db_connection->close_connection();
            $form_errors = "<p>Lo username inserito non esiste.</p>";
            $html_page = input_restore();
            echo str_replace(["[err_utenti_creazione]", "[err_utenti_elimina]"], ["", $form_errors], $html_page);
            exit();
        }
        $result = $db_connection->admin_delete_user($_POST["username_delete"]);
        $db_connection->close_connection();

        if (!$result) {
            $form_errors = "<p>Errore durante l'eliminazione dell'utente.</p>";
            $html_page = input_restore();
            echo str_replace(["[err_utenti_creazione]", "[err_utenti_elimina]"], ["", $form_errors], $html_page);
            exit();
        } else {
            header("location: area_amministratore.php");
        }
    } catch (Exception) {
        header("location: ../pages/500.html");
        exit();
    }
    echo str_replace(["[err_utenti_creazione]", "[err_utenti_elimina]"], ["", $form_errors], $html_page);
    exit();
} else {
    $html_page = input_restore();
    echo str_replace(["[err_utenti_creazione]", "[err_utenti_elimina]"], $form_errors, $html_page);
    exit();
}

echo $html_page;
?>