<?php
require_once 'db_connection.php';
use DB\DBConnection;

$html_page = file_get_contents('../pages/form_classifiche_admin.html');
$form_errors = "";

function input_restore() {
    $html_page = file_get_contents("../pages/form_classifiche_admin.html");
    $html_page = str_replace("[punti]", htmlspecialchars(isset($_POST['punti']) ? $_POST['punti'] : ''), $html_page);
    return $html_page;
}

if (isset($_POST["modifica_punti_pilota"])) {
    if (empty($_POST["pilota_to_increase"]) || empty($_POST["azione"]) || empty($_POST["punti"])) {
        $form_errors = "<p>Devi compilare tutti i campi.</p>";
        $html_page = input_restore();
        echo str_replace("[err_classifica_modifica]", $form_errors, $html_page);
        exit();
    }

    $id_pilota = $_POST["pilota_to_increase"];
    $azione = $_POST["azione"];
    $punti = intval($_POST["punti"]);

    try {
        $db_connection = new DBConnection();
        $pilotExist = $db_connection->check_for_existing_pilot($id_pilota);
        if ($pilotExist) {
            $db_connection->close_connection();
            $form_errors = "<p>Il pilota inserito non esiste.</p>";
            $html_page = input_restore();
            echo str_replace("[err_classifica_modifica]", $form_errors, $html_page);
            exit();
        }

        if ($azione === "aumenta") {
            $result = $db_connection->admin_increase_pilot_points($id_pilota, $punti);
        } else if ($azione === "diminuisci") {
            $result = $db_connection->admin_decrease_pilot_points($id_pilota, $punti);
        } else {
            $db_connection->close_connection();
            $form_errors = "<p>Azione non valida.</p>";
            $html_page = input_restore();
            echo str_replace("[err_classifica_modifica]", $form_errors, $html_page);
            exit();
        }

        $db_connection->close_connection();

        // Se refresh pagina non re invia il comando già fatto (qui es. non aumenta di nuovo i punti)
        header("Location: " . $_SERVER['PHP_SELF'] . "?msg=ok");
        exit();
    } catch (Exception) {
        header("location: ../pages/500.html");
        exit();
    }
    $html_page = input_restore();
} else {
    $html_page = input_restore();

    // Mostra messaggio di successo se i punti sono stati modificati con successo
    if (isset($_GET['msg'])) {
        $message = "<p>Punti del pilota modificati con successo!</p>";
        echo str_replace("[err_classifica_modifica]", $message, $html_page);
        exit();
    } else {
        echo str_replace("[err_classifica_modifica]", $form_errors, $html_page);
        exit();
    }
}
?>