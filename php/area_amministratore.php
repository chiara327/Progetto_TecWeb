<?php
require_once 'db_connection.php';
use DB\DBConnection;

session_start();

// 1. Controllo Accesso: se non è loggato, rimanda al login reale
if (!isset($_SESSION["user"])) {
    header("location: login.php");
    exit();
}

$html_page = file_get_contents("../pages/area_amministratore.html");
$username_sessione = $_SESSION["user"];
$status_message = ""; 

try {
    // --- GESTIONE LOGOUT ---
    if (isset($_POST["logout"])) {
        session_destroy();
        header("location: login.html");
        exit();
    }

    // --- GESTIONE AGGIORNAMENTO PROFILO ---
    if (isset($_POST["nome"]) && isset($_POST["cognome"]) && isset($_POST["data-nascita"])) {
        $nome = trim($_POST["nome"]);
        $cognome = trim($_POST["cognome"]);
        $data_nascita = $_POST["data-nascita"]; // Formato YYYY-MM-DD dall'input date

        // Validazione (Stessa logica della registrazione)
        if (empty($nome) || empty($cognome) || empty($data_nascita)) {
            $status_message = "<p class='error'>Tutti i campi sono obbligatori.</p>";
        } elseif (!preg_match("/^[A-Za-zÀ-ÿ\s\-\']+$/", $nome) || !preg_match("/^[A-Za-zÀ-ÿ\s\-\']+$/", $cognome)) {
            $status_message = "<p class='error'>Il nome e il cognome devono contenere solo lettere.</p>";
        } elseif (strtotime($data_nascita) > strtotime(date("Y-m-d"))) {
            $status_message = "<p class='error'>La data di nascita non può essere nel futuro.</p>";
        } else {
            // Chiamata al metodo della classe DBConnection
            $db_connection = new DBConnection();
            $db_connection->update_user_profile($username_sessione, $nome, $cognome, $data_nascita);
            $db_connection->close_connection();
            $status_message = "<p class='success'>Informazioni aggiornate con successo!</p>";
        }
    }
    
    // --- RECUPERO DATI UTENTE ---
    $db_connection = new DBConnection();
    $user_data = $db_connection->get_user_info($username_sessione);

    // --- GENERAZIONE DINAMICA COMMENTI ---
    $commenti_data = $db_connection->get_user_comments($username_sessione);
    $db_connection->close_connection();

    $commenti_html = "";

    if (empty($commenti_data)) {
        $commenti_html = "<li>Non hai ancora postato alcun commento.</li>";
    } else {
        foreach ($commenti_data as $comm) {
            $data_formattata = date("d/m/Y", strtotime($comm['data']));
            $commenti_html .= "<li>
                <blockquote cite='#'>
                    <p><q>" . htmlspecialchars($comm['testo']) . "</q></p>
                    <footer>Postato il <time datetime='{$comm['data']}'>{$data_formattata}</time></footer>
                </blockquote>
            </li>";
        }
    }

    // --- MAPPA DELLE SOSTITUZIONI ---
    $sostituzioni = [
        "[username]"         => htmlspecialchars($user_data['username']),
        "[nome]"             => htmlspecialchars($user_data['nome']),
        "[cognome]"          => htmlspecialchars($user_data['cognome']),
        "[data di nascita]"  => date("d/m/Y", strtotime($user_data['dataNascita'])), // Testo leggibile
        "[data-formato-iso]" => $user_data['dataNascita'], // Per attributo value dell'input date
        "[err]"              => $status_message
    ];

    // Sostituzioni testuali e form
    foreach ($sostituzioni as $placeholder => $valore) {
        $html_page = str_replace($placeholder, $valore, $html_page);
    }

    // Sostituzione della lista commenti
    // Cerchiamo il blocco <li> d'esempio nel tuo HTML per rimpiazzarlo con quelli reali
    $blocco_commento_esempio = '<li>
                    <blockquote cite="#">
                        <p><q>[commento 1]</q></p>
                        <footer>Postato il <time datetime="2023-10-27">[data 1]</time></footer>
                    </blockquote>
                </li>';
    
    $html_page = str_replace("[lista-commenti]", $commenti_html, $html_page);

    echo $html_page;

} catch (Exception $e) {
    // In caso di errore grave (es. database offline)
    error_log($e->getMessage());
    header("location: ../pages/500.html");
    exit();
}
?>
