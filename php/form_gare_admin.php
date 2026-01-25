<?php
require_once 'db_connection.php';
use DB\DBConnection;

$html_page = file_get_contents('../pages/form_gare_admin.html');
$form_errors = "";
$form_errors_delete = "";

function check_invalid_input($data, $primo_posto, $secondo_posto, $terzo_posto) {
	global $form_errors;
	$parti = explode("-", $data);
	$anno = $parti[0]; // Prende la prima parte prima del trattino
	if (!($anno == "2025" || $anno == "2026") || strtotime($data) > strtotime(date("Y-m-d"))) {
		$form_errors = $form_errors . "<p>La data inserita non è valida. Deve appartenere agli anni 2025 o 2026 e non essere nel futuro.</p>";
	}
	if($primo_posto === $secondo_posto || $primo_posto === $terzo_posto || $secondo_posto === $terzo_posto) {
		$form_errors = $form_errors . "<p>I piloti non possono occupare più di una posizione.</p>";
	}
}

function input_restore() {
    $html_page = file_get_contents("../pages/form_gare_admin.html");
    $html_page = str_replace("[data]", htmlspecialchars(isset($_POST['data']) ? $_POST['data'] : ''), $html_page);
    $html_page = str_replace("[primo_posto]", htmlspecialchars(isset($_POST['primo_posto']) ? $_POST['primo_posto'] : ''), $html_page);
    $html_page = str_replace("[secondo_posto]", htmlspecialchars(isset($_POST['secondo_posto']) ? $_POST['secondo_posto'] : ''), $html_page);
    $html_page = str_replace("[terzo_posto]", htmlspecialchars(isset($_POST['terzo_posto']) ? $_POST['terzo_posto'] : ''), $html_page);
    return $html_page;
}

if (isset($_POST["creazione_gare"])) {
    if (empty($_POST["id_circuito"]) || empty($_POST["data"]) || empty($_POST["primo_posto"]) || empty($_POST["secondo_posto"]) || empty($_POST["terzo_posto"])) {
        $form_errors = "<p>Devi compilare tutti i campi.</p>";
    } else {
		
		check_invalid_input($_POST["data"], $_POST["primo_posto"], $_POST["secondo_posto"], $_POST["terzo_posto"]);

        if (empty($form_errors)) {
            try {
                $db_connection = new DBConnection();
                $db_connection->admin_add_race($_POST["id_circuito"], $_POST["data"], $_POST["primo_posto"], $_POST["secondo_posto"], $_POST["terzo_posto"]);
                $db_connection->close_connection();
                
                header("Location: " . $_SERVER['PHP_SELF'] . "?res=ok");
                exit();
            } catch (Exception) {
                header("Location: ../pages/500.html");
                exit();
            }
        }
    }
} else if (isset($_POST["elimina_gare"])) {
    if (empty($_POST["id_delete"])) {
        $form_errors_delete = "<p>Devi inserire un ID gara da eliminare.</p>";
    } else {
        try {
            $db_connection = new DBConnection();
            $raceExist = $db_connection->check_for_existing_race($_POST["id_delete"]);
            if ($raceExist) {
                $db_connection->close_connection();
                $form_errors_delete = "<p>La gara inserita non esiste.</p>";
            } else {
                $result = $db_connection->admin_delete_race($_POST["id_delete"]);
                $db_connection->close_connection();
                if (!$result) {
                    $form_errors_delete = "<p>Errore durante l'eliminazione della gara.</p>";
                } else {
                    header("Location: " . $_SERVER['PHP_SELF'] . "?res=del");
                    exit();
                }
            }
        } catch (Exception) {
            header("Location: ../pages/500.html");
            exit();
        }
    }
}

if (isset($_GET['res'])) {
    if ($_GET['res'] == 'ok') $form_errors = "Gara creata con successo!";
    if ($_GET['res'] == 'del') $form_errors_delete = "Gara eliminata con successo!";
}

$html_page = input_restore();
echo str_replace(["[err_gare_creazione]", "[err_gare_elimina]"], [$form_errors, $form_errors_delete], $html_page);
?>