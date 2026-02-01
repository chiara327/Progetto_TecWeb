<?php
require_once 'db_connection.php';
use DB\DBConnection;

$html_page = file_get_contents('../pages/form_classifiche_admin.html');
$dropdown_piloti = "";
$form_errors = "";

function input_restore() {
    $html_page = file_get_contents("../pages/form_classifiche_admin.html");
    $html_page = str_replace("[punti]", htmlspecialchars(isset($_POST['punti']) ? $_POST['punti'] : ''), $html_page);
    return $html_page;
}

function check_invalid_input($punti) {
	global $form_errors;

    if (!is_numeric($punti) || intval($punti) < 0) {
        $form_errors .= "<p>I punti devono essere un numero intero positivo.</p>";
    }
}

function create_dropdown_menus() {
    $db_connection = new DBConnection();
    $piloti_data = $db_connection->get_all_piloti();
    $db_connection->close_connection();

    $piloti_list = "";

    foreach ($piloti_data as $pilota) {
        $id = $pilota["id"];
        $full_name = htmlspecialchars($pilota["nome"] . " " . $pilota["cognome"]);

        $lang_code = htmlspecialchars($pilota["nazionalita"]); 
        
        $piloti_list .= "<option value=\"$id\" lang=\"$lang_code\">$full_name</option>";
    }

    return $piloti_list;
}

if (isset($_POST["modifica_punti_pilota"])) {
    if (empty($_POST["pilota_to_increase"]) || !isset($_POST["azione"]) || !isset($_POST["punti"])) {
        $form_errors = "<p>Devi compilare tutti i campi.</p>";
    } else {
        check_invalid_input($_POST["punti"]);
        
        if (empty($form_errors)) {
            $id_pilota = $_POST["pilota_to_increase"];
            $azione = $_POST["azione"];
            $punti = intval($_POST["punti"]);

            try {
                $db_connection = new DBConnection();

                if ($azione === "diminuisci" && !$db_connection->check_for_enough_points($id_pilota, $punti)) {
                    $form_errors = "<p>Non puoi togliere più punti di quelli che il pilota ha.</p>";
                } else {
                    if ($azione === "aumenta") {
                        $db_connection->admin_increase_pilot_points($id_pilota, $punti);
                    } else if ($azione === "diminuisci") {
                        $db_connection->admin_decrease_pilot_points($id_pilota, $punti);
                    }
                    
                    $db_connection->close_connection();
                    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=ok");
                    exit();
                }
                $db_connection->close_connection();
            } catch (Exception $e) {
                header("location: ../pages/500.html");
                exit();
            }
        }
    }
}

$html_page = input_restore();
$dropdown_piloti = create_dropdown_menus();

if (isset($_GET['msg']) && $_GET['msg'] === 'ok') {
    $form_errors = "<p>Punti del pilota modificati con successo!</p>";
}

echo str_replace(["[err_classifica_modifica]", "[piloti_dropdown]"], [$form_errors, $dropdown_piloti], $html_page);
?>