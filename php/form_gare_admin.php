<?php
require_once 'db_connection.php';
use DB\DBConnection;

$html_page = file_get_contents('../pages/form_utenti_admin.html');
$form_errors = "";

function input_restore() {
	$html_page = file_get_contents("../pages/form_gare_admin.html");
	$html_page = str_replace("[id]", htmlspecialchars(isset($_POST['id']) ? $_POST['id'] : ''), $html_page);
	$html_page = str_replace("[id_circuito]", htmlspecialchars(isset($_POST['id_circuito']) ? $_POST['id_circuito'] : ''), $html_page);
	$html_page = str_replace("[data]", htmlspecialchars(isset($_POST['data']) ? $_POST['data'] : ''), $html_page);
	$html_page = str_replace("[primo_posto]", htmlspecialchars(isset($_POST['primo_posto']) ? $_POST['primo_posto'] : ''), $html_page);
	$html_page = str_replace("[secondo_posto]", htmlspecialchars(isset($_POST['secondo_posto']) ? $_POST['secondo_posto'] : ''), $html_page);
	$html_page = str_replace("[terzo_posto]", htmlspecialchars(isset($_POST['terzo_posto']) ? $_POST['terzo_posto'] : ''), $html_page);
	return $html_page;
}

// TODO: NON FUNZIONA ELIMINA UTENTE, GLI ERRORI IN PLACEHOLDER NON VANNO
function check_invalid_input($id, $id_circuito, $data, $primo_posto, $secondo_posto, $terzo_posto) {
	global $form_errors;
	// TODO: IMPLEMENTARE CONTROLLI PER LE GARE (Anche in db_connection.php, soprattutto per ID univoco e se esistono gli ID piloti e circuito)
}

if (isset($_POST["creazione_gare"])) {
    if (empty($_POST["id"]) || empty($_POST["id_circuito"]) || empty($_POST["data"]) || empty($_POST["primo_posto"]) || empty($_POST["secondo_posto"]) || empty($_POST["terzo_posto"])) {
		$form_errors = $form_errors . "<p>Devi compilare tutti i campi.</p>";
		$html_page = input_restore();
		echo str_replace(["[err_gare_creazione]", "[err_gare_elimina]"], [$form_errors, ""], $html_page);
		exit();
	} else {
		// Validazione degli input, se riscontra errori li segnala in $form_errors
		check_invalid_input($_POST["id"], $_POST["id_circuito"], $_POST["data"], $_POST["primo_posto"], $_POST["secondo_posto"], $_POST["terzo_posto"]);
		// Ha segnalato errori negli input
		if (!empty($form_errors)) {
			$html_page = input_restore();
			echo str_replace(["[err_gare_creazione]", "[err_gare_elimina]"], [$form_errors, ""], $html_page);
			exit();
		}

		// Transaction per registrare nuovo utente
		try {
			$db_connection = new DBConnection();
			$result = $db_connection->admin_add_race($_POST["id"], $_POST["id_circuito"], $_POST["data"], $_POST["primo_posto"], $_POST["secondo_posto"], $_POST["terzo_posto"]);
			$db_connection->close_connection();

			// TODO: GESTIRE ERRORE ID GIA ESISTENTE IN QUESTO BELLISSIMO IF COMMENTATO (implementare anche in db_connection.php)
			/*if (!$result) {
				$form_errors = $form_errors . "<p>Lo <span lang='en'>username</span> che hai scelto &egrave; stato già registrato.</p>";
				$html_page = input_restore();
				echo str_replace(["[err_gare_creazione]", "[err_gare_elimina]"], [$form_errors, ""], $html_page);
				exit();
			} else {
				header("location: area_amministratore.php");
			}*/
			header("location: area_amministratore.php"); //ricordati di eliminare questo quando hai fatto if
		} catch (Exception) {
			header("location: ../pages/500.html");
			exit();
		}
	}
    echo str_replace(["[err_gare_creazione]", "[err_gare_elimina]"], [$form_errors, ""], $html_page);
	
    exit();
} else if (isset($_POST["elimina_gare"])) {
    if (empty($_POST["id_delete"])) {
        $form_errors = "<p>Devi inserire un ID gara da eliminare.</p>";
        $html_page = input_restore();
        echo str_replace(["[err_gare_creazione]", "[err_gare_elimina]"], ["", $form_errors], $html_page);
        exit();
    }
    try {
        $db_connection = new DBConnection();
        $raceExist = $db_connection->check_for_existing_race($_POST["id_delete"]);
        if ($raceExist) {
            $db_connection->close_connection();
            $form_errors = "<p>La gara inserita non esiste.</p>";
            $html_page = input_restore();
            echo str_replace(["[err_gare_creazione]", "[err_gare_elimina]"], ["", $form_errors], $html_page);
            exit();
        }
        $result = $db_connection->admin_delete_race($_POST["id_delete"]);
        $db_connection->close_connection();

        if (!$result) {
            $form_errors = "<p>Errore durante l'eliminazione della gara.</p>";
            $html_page = input_restore();
            echo str_replace(["[err_gare_creazione]", "[err_gare_elimina]"], ["", $form_errors], $html_page);
            exit();
        } else {
            header("location: area_amministratore.php");
        }
    } catch (Exception) {
        header("location: ../pages/500.html");
        exit();
    }
    echo str_replace(["[err_gare_creazione]", "[err_gare_elimina]"], ["", $form_errors], $html_page);
    exit();
} else {
    $html_page = input_restore();
    echo str_replace(["[err_gare_creazione]", "[err_gare_elimina]"], $form_errors, $html_page);
    exit();
}

echo $html_page;
?>