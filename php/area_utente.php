<?php
require_once 'db_connection.php';
use DB\DBConnection;

session_start();

// 1. Controllo Accesso
if (!isset($_SESSION["user"])) {
    header("location: login.php");
    exit();
}

$anagrafica_errors = "";
$sicurezza_errors = "";

// --- LOGICA DI AGGIORNAMENTO ANAGRAFICA ---
if (isset($_POST["salva_modifiche"])) {
    $nome = trim($_POST["nome"]);
    $cognome = trim($_POST["cognome"]);
    $data = $_POST["data"];

    if (empty($nome) || empty($cognome) || empty($data)) {
        $anagrafica_errors = "<p class='error'>Tutti i campi anagrafici sono obbligatori.</p>";
    } else {
        // Validazione (puoi usare la funzione preg_match vista prima)
        $db_connection = new DBConnection();
        $res = $db_connection->update_user_info($_SESSION["user"], $nome, $cognome, $data);
        $db_connection->close_connection();
        if ($res) {
            header("Location: area_utente.php?status=ok_anag");
            exit();
        } else {
            $anagrafica_errors = "<p class='error'>Errore nell'aggiornamento dei dati.</p>";
        }
    }
}

// --- LOGICA DI AGGIORNAMENTO SICUREZZA ---
if (isset($_POST["password-attuale"])) {
    $nuovo_user = trim($_POST["nuovo-username"]);
    $nuova_pw = $_POST["nuova-password"];
    $pw_attuale = $_POST["password-attuale"];

    // Qui andrebbe la logica di verifica password attuale e update
    // Esempio semplificato:
    $db_connection = new DBConnection();
    $verify = $db_connection->verify_password($_SESSION["user"], $pw_attuale);
    if ($verify) {
        if (!empty($nuovo_user)) {
            $db_connection->update_username($_SESSION["user"], $nuovo_user);
            $_SESSION["user"] = $nuovo_user; // Aggiorno la sessione
        }
        if (!empty($nuova_pw)) {
            $db_connection->update_password($_SESSION["user"], $nuova_pw);
        }
        header("Location: area_utente.php?status=ok_sec");
        exit();
    } else {
        $sicurezza_errors = "<p class='error'>Password attuale errata.</p>";
    }
    $db_connection->close_connection();
}

// --- RECUPERO DATI PER RENDERING ---
$db_connection = new DBConnection();
$user_data = $db_connection->get_user_info($_SESSION["user"]);
$commenti_data = $db_connection->get_user_comments($_SESSION["user"]);
$db_connection->close_connection();

// --- COSTRUZIONE HTML ---
$html_page = file_get_contents("../pages/area_utente.html");

// 1. Sostituzione Errori
$html_page = str_replace("[err-anag]", $anagrafica_errors, $html_page);
$html_page = str_replace("[err-sicurezza]", $sicurezza_errors, $html_page);

// 2. Dati Anagrafici (Header e Lista)
$html_page = str_replace("[username]", htmlspecialchars($_SESSION["user"]), $html_page);
$html_page = str_replace("[nome]", htmlspecialchars($user_data['nome']), $html_page);
$html_page = str_replace("[cognome]", htmlspecialchars($user_data['cognome']), $html_page);

// 3. Gestione Date (Tre formati diversi per l'HTML)
$data_nascita = $user_data['dataNascita']; // formato YYYY-MM-DD
$html_page = str_replace("[data-formato-iso]", $data_nascita, $html_page); // Per datetime
$html_page = str_replace("[data di nascita]", date("d/m/Y", strtotime($data_nascita)), $html_page); // Per la lista <dd>
$html_page = str_replace("[data]", $data_nascita, $html_page); // Per l'input type="date"

// 4. Generazione Lista Commenti
$commenti_html = "";
if (empty($commenti_data)) {
    $commenti_html = "<li>Non hai ancora postato alcun commento.</li>";
} else {
    foreach ($commenti_data as $comm) {
        $data_c = date("d/m/Y", strtotime($comm['data']));
        $commenti_html .= "<li>
            <blockquote cite='#'>
                <p><q>" . htmlspecialchars($comm['testo']) . "</q></p>
                <footer>Postato il <time datetime='{$comm['data']}'>{$data_c}</time></footer>
            </blockquote>
        </li>";
    }
}
$html_page = str_replace("[lista-commenti]", $commenti_html, $html_page);

echo $html_page;
?>