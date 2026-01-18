<?php
require_once 'db_connection.php';
use DB\DBConnection;

session_start();

if (!isset($_SESSION["user"])) {
    header("location: login.php");
    exit();
}

$anagrafica_errors = "";
$password_errors = "";
$username_errors = "";
$success_msg = "";

// Gestione messaggi di successo dai redirect
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'ok_anag') $success_msg = "<p class='success'>Dati anagrafici aggiornati correttamente.</p>";
    if ($_GET['status'] == 'ok_sec') $success_msg = "<p class='success'>Credenziali aggiornate con successo.</p>";
}

// --- LOGICA DI AGGIORNAMENTO ANAGRAFICA ---
if (isset($_POST["salva_modifiche"])) {
    $nome = trim($_POST["nome"]);
    $cognome = trim($_POST["cognome"]);
    $data = $_POST["data"];

    // 1. Controllo campi vuoti
    if (empty($nome) || empty($cognome) || empty($data)) {
        $anagrafica_errors .= "<p class='error'>Tutti i campi anagrafici sono obbligatori.</p>";
    } else {
        // 2. Validazione Struttura Dati
        if (!preg_match("/^[A-Za-zÀ-ÿ\s\-\']+$/", $nome)) {
            $anagrafica_errors .= "<p class='error'>Il nome contiene caratteri non validi.</p>";
        }
        if (!preg_match("/^[A-Za-zÀ-ÿ\s\-\']+$/", $cognome)) {
            $anagrafica_errors .= "<p class='error'>Il cognome contiene caratteri non validi.</p>";
        }
        if (strtotime($data) > time()) {
            $anagrafica_errors .= "<p class='error'>La data di nascita non può essere nel futuro.</p>";
        }

        // 3. Se non ci sono errori, procedo al database
        if (empty($anagrafica_errors)) {
            $db_connection = new DBConnection();
            if ($db_connection->update_user_info($_SESSION["user"], $nome, $cognome, $data)) {
                $db_connection->close_connection();
                header("Location: area_utente.php?status=ok_anag");
                exit();
            } else {
                $anagrafica_errors .= "<p class='error'>Errore nel salvataggio dei dati.</p>";
            }
            $db_connection->close_connection();
        }
    }
}

// --- LOGICA DI AGGIORNAMENTO SICUREZZA ---
$db_connection = new DBConnection();

// --- BLOCCO 1: AGGIORNAMENTO USERNAME ---
if (isset($_POST["modifica_username"])) {
    $nuovo_user = trim($_POST["nuovo-username"]);
    $pw_attuale = $_POST["password-attuale"];

    // 1. Verifica password attuale
    if ($db_connection->verify_password($_SESSION["user"], $pw_attuale)) {
        if (!empty($nuovo_user) && $nuovo_user !== $_SESSION["user"]) {
            if (strlen($nuovo_user) > 30) {
                $username_errors = "<p class='error'>Lo username non può superare i 30 caratteri.</p>";
            } else {
                if ($db_connection->update_username($_SESSION["user"], $nuovo_user)) {
                    $_SESSION["user"] = $nuovo_user;
                    header("Location: area_utente.php?status=ok_user");
                    exit();
                } else {
                    $username_errors = "<p class='error'>Lo username scelto è già in uso.</p>";
                }
            }
        }
    } else {
        $username_errors = "<p class='error'>Password attuale errata.</p>";
    }
}

// --- BLOCCO 2: AGGIORNAMENTO PASSWORD ---
if (isset($_POST["modifica_password"])) {
    $nuova_pw = $_POST["nuova-password"];
    $pw_attuale = $_POST["password-attuale"];

    // 1. Verifica password attuale
    if ($db_connection->verify_password($_SESSION["user"], $pw_attuale)) {
        if (!empty($nuova_pw)) {
            // Regex: 8 car, 1 Maiusc, 1 minusc, 1 numero, 1 speciale
            if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $nuova_pw)) {
                $password_errors = "<p class='error'>La password deve avere 8 caratteri, una maiuscola, un numero e un speciale.</p>";
            } else {
                $hash = password_hash($nuova_pw, PASSWORD_DEFAULT);
                if ($db_connection->update_password($_SESSION["user"], $hash)) {
                    header("Location: area_utente.php?status=ok_pass");
                    exit();
                }
            }
        }
    } else {
        $password_errors = "<p class='error'>Password attuale errata.</p>";
    }
}

$db_connection->close_connection();

// --- RECUPERO DATI E RENDERING ---
$db_connection = new DBConnection();
$user_data = $db_connection->get_user_info($_SESSION["user"]);
$commenti_data = $db_connection->get_user_comments($_SESSION["user"]);
$db_connection->close_connection();

$html_page = file_get_contents("../pages/area_amministratore.html");

// Sostituzioni segnaposto
$html_page = str_replace("[err-anag]", $anagrafica_errors, $html_page);
$html_page = str_replace("[err-username]", $username_errors, $html_page);
$html_page = str_replace("[err-password]", $password_errors, $html_page);
$html_page = str_replace("[messaggio-successo]", $success_msg, $html_page);

$html_page = str_replace("[username]", htmlspecialchars($_SESSION["user"]), $html_page);
$html_page = str_replace("[nome]", htmlspecialchars($user_data['nome']), $html_page);
$html_page = str_replace("[cognome]", htmlspecialchars($user_data['cognome']), $html_page);

$data_nascita = $user_data['dataNascita'];
$html_page = str_replace("[data di nascita]", date("d/m/Y", strtotime($data_nascita)), $html_page);
$html_page = str_replace("[data]", $data_nascita, $html_page);

// Commenti
$commenti_html = "";
if (empty($commenti_data)) {
    $commenti_html = "<li>Non hai ancora postato alcun commento.</li>";
} else {
    foreach ($commenti_data as $comm) {
        $testo = htmlspecialchars($comm['testo']);
        $gara = htmlspecialchars($comm['nome_gara']);
        $data_iso = $comm['data'];
        $data_it = date("d/m/Y", strtotime($comm['data']));

        $commenti_html .= "<li>
            <article class='commento-card'>
                <header>
                    <h3>Commento su: $gara</h3>
                    <p class='comment-date'>Pubblicato il <time datetime='$data_iso'>$data_it</time></p>
                </header>
                <p class='comment-content'>$testo</p>
            </article>
        </li>";
    }
}

$html_page = str_replace("[lista-commenti]", $commenti_html, $html_page);

echo $html_page;
?>