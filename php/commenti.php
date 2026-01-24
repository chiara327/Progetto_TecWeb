<?php
require_once 'db_connection.php';
use DB\DBConnection;

session_start();

// TODO: rimuovere questa linea una volta implementato il link da gara.php
$_SESSION["id_gara"] = 1;

/*if (!isset($_SESSION["user"])) {
    header("location: login.php");
    exit();
}*/

$gara = "";
$commenti_errors = "";
$success_msg = "";

// --- RECUPERO DATI E RENDERING ---
$db_connection = new DBConnection();
$commenti_data = $db_connection->get_commenti($_SESSION["id_gara"]);
$db_connection->close_connection();

$html_page = file_get_contents("../pages/commenti.html");

// Sostituzioni segnaposto
$html_page = str_replace("[err-commenti]", $commenti_errors, $html_page);
$html_page = str_replace("[messaggio-successo]", $success_msg, $html_page);

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
$html_page = str_replace("[gara]", $gara, $html_page);
$html_page = str_replace("[dettagli-gara]", $gara, $html_page);

echo $html_page;
?>