<?php
require_once 'db_connection.php';
use DB\DBConnection;

session_start();

// 1. GESTIONE ID GARA (Recupero da POST o da GET per il redirect)
// Usiamo gara_id coerentemente con il form della pagina precedente
$id_gara_attuale = null;
if (isset($_POST['gara_id'])) {
    $id_gara_attuale = intval($_POST['gara_id']);
} elseif (isset($_GET['id_gara'])) {
    $id_gara_attuale = intval($_GET['id_gara']);
}

// Se non abbiamo un ID, torniamo indietro
if (!$id_gara_attuale) {
    header("location: gare.php");
    exit();
}

$commenti_errors = "";
$success_msg = "";
$err_aggiungi_commenti = "";
$messaggio_successo_aggiunta_commento = "";

// 2. LOGICA DI INVIO NUOVO COMMENTO (Prima del recupero dati)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["invia_commento"])) {
    if (isset($_SESSION["user"])) {
        $testo = trim($_POST["testo_commento"]);
        $username = $_SESSION["user"];
        $data_oggi = date("Y-m-d H:i:s");

        if (empty($testo)) {
            $err_aggiungi_commenti = "<p class='error' aria-live='assertive'>Il commento non può essere vuoto.</p>";
        } else {
            try {
                $db = new DBConnection();
                if ($db->insert_commento($username, $id_gara_attuale, $testo, $data_oggi)) {
                    $db->close_connection();
                    // Passiamo l'id_gara nel GET per non perderlo dopo il redirect
                    header("Location: commenti.php?status=ok&id_gara=" . $id_gara_attuale); 
                    exit();
                } else {
                    $err_aggiungi_commenti = "<p class='error' aria-live='assertive'>Errore nella pubblicazione.</p>";
                }
                $db->close_connection();
            } catch (Exception $e) {
                header("location: ../pages/500.html");
                exit();
            }
        }
    }
}

if (isset($_GET['status']) && $_GET['status'] === 'ok') {
    $messaggio_successo_aggiunta_commento = "<p class='success' aria-live='polite'>Commento pubblicato!</p>";
}

// 3. RECUPERO DATI PER RENDERING
try {
    $db_connection = new DBConnection();
    $gara_data = $db_connection->get_gara_data($id_gara_attuale);
    $commenti_data = $db_connection->get_commenti($id_gara_attuale);
    $db_connection->close_connection();
} catch (Exception $e) {
    header("location: ../pages/500.html");
    exit();
}

// Se per qualche motivo l'ID non esiste nel DB
if (!$gara_data) {
    header("location: gare.php");
    exit();
}

$html_page = file_get_contents("../pages/commenti.html");

// 4. COSTRUZIONE INTERFACCIA
$data_val = $gara_data['data'];
$data_it = date("d/m/Y", strtotime($data_val));
$nazione = htmlspecialchars($gara_data['circuito_nazione']);
$anno = date("Y", strtotime($data_val));
$titolo_gp = $nazione . ' <span lang="en">Grand Prix</span> ' . $anno;

$p1 = htmlspecialchars($gara_data['p1_nome'] . " " . $gara_data['p1_cognome']);
$p2 = htmlspecialchars($gara_data['p2_nome'] . " " . $gara_data['p2_cognome']);
$p3 = htmlspecialchars($gara_data['p3_nome'] . " " . $gara_data['p3_cognome']);

$info_gara_html = "
    <h2 class='commento-titolo'>" . htmlspecialchars($gara_data['circuito_nome']) . "</h2>
    <dl class='gp-stats'>
        <dt>Città</dt>
        <dd>" . htmlspecialchars($gara_data['circuito_citta']) . "</dd>
        <dt>Nazione</dt>
        <dd>" . htmlspecialchars($gara_data['circuito_nazione']) . "</dd>
        <dt>Data</dt>
        <dd>$data_it</dd>
        <dt>Lunghezza</dt>
        <dd>" . htmlspecialchars($gara_data['circuito_lunghezza']) . " m</dd>
        <dt>Curve</dt>
        <dd>" . htmlspecialchars($gara_data['circuito_curve']) . "</dd>
    </dl>
    <ol class='podium-summary' aria-label='Podio della gara'>
        <li class='podium-item gold'>
            <span class='rank' aria-hidden='true'>1</span>
            <span class='driver'>$p1</span>
        </li>
        <li class='podium-item silver'>
            <span class='rank' aria-hidden='true'>2</span>
            <span class='driver'>$p2</span>
        </li>
        <li class='podium-item bronze'>
            <span class='rank' aria-hidden='true'>3</span>
            <span class='driver'>$p3</span>
        </li>
    </ol>";

// COSTRUZIONE FORM
$form_commento = "";
if (isset($_SESSION["user"])) {
    $form_commento = '
        <h2 class="commento-titolo">Aggiungi un Commento</h2>
        <form id="form-commento" action="commenti.php" method="post">
            <input type="hidden" name="gara_id" value="' . $id_gara_attuale . '">
            <label for="testo-commento">Stai commentando come: <strong>' . htmlspecialchars($_SESSION["user"]) . '</strong></label>
            <textarea id="testo-commento" name="testo_commento" rows="4" required aria-required="true"></textarea>
            <button type="submit" name="invia_commento">Pubblica Commento</button>
        </form>
        ' . $err_aggiungi_commenti . '
        ' . $messaggio_successo_aggiunta_commento . '';
} else {
    $form_commento = '<section class="avviso-login"><p><a href="login.php">Accedi</a> per commentare.</p></section>';
}

// 5. SOSTITUZIONI
//$html_page = str_replace("[GP]", $gara_data['circuito_citta'] . " Grand Prix", $html_page);
$html_page = str_replace("[form-commento]", $form_commento, $html_page);
$html_page = str_replace("[dettagli-gara]", $info_gara_html, $html_page);
$html_page = str_replace("[titolo-gp]", $titolo_gp, $html_page);
//$html_page = str_replace("[gara]", htmlspecialchars($gara_data['circuito_nome'] . " " . $anno), $html_page);

// Generazione lista commenti
$commenti_html = "";
if (empty($commenti_data)) {
    $commenti_html = "<li>Non è ancora stato postato alcun commento.</li>";
} else {
    foreach ($commenti_data as $comm) {
        $testo = htmlspecialchars($comm['testo']);
        $utente = htmlspecialchars($comm['username']);
        $timestamp = strtotime($comm['data_ora']); 
        $data_iso = date("Y-m-d\TH:i", $timestamp); 
        $data_it = date("d/m/Y", $timestamp);       
        $ora_it = date("H:i", $timestamp);
        $commenti_html .= "<li>
            <article class='commento-card'>
                <header>
                    <h3>Commento di: $utente</h3>
                    <p class='comment-date'>
                        Pubblicato il <time datetime='$data_iso'>$data_it</time> alle $ora_it
                    </p>
                </header>
                <p class='comment-content'>$testo</p>
            </article>
        </li>";
    }
}
$html_page = str_replace("[lista-commenti]", $commenti_html, $html_page);

echo $html_page;
?>