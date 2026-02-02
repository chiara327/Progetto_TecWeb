<?php
require_once 'db_connection.php';
use DB\DBConnection;

session_start();

// 1. GESTIONE ID GARA (Recupero da POST o da GET per il redirect)
$id_gara_attuale = null;
if (isset($_POST['gara_id'])) {
    $id_gara_attuale = intval($_POST['gara_id']);
} elseif (isset($_GET['id_gara'])) {
    $id_gara_attuale = intval($_GET['id_gara']);
}

// Se non abbiamo un ID, torniamo alla pagina delle gare
if (!$id_gara_attuale) {
    header("location: gare.php");
    exit();
}

$commenti_errors = "";
$success_msg = "";
$err_aggiungi_commenti = "";
$messaggio_successo_aggiunta_commento = "";
$err_eliminazione = "";

// 2. LOGICA DI INVIO NUOVO COMMENTO
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

// 3. LOGICA DI ELIMINAZIONE COMMENTO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'])) {
    if (isset($_SESSION['user'])) {
        $id_da_eliminare = $_POST['comment_id'];
        $utente_attivo = $_SESSION['user'];

        try {
            $db_connection = new DBConnection();
            $risultato_eliminazione = $db_connection->delete_commento($id_da_eliminare, $utente_attivo);
            $db_connection->close_connection();
            
            if ($risultato_eliminazione === true) {
                $err_eliminazione = "<p class='success'>Commento eliminato con successo.</p>";
            } else {
                $err_eliminazione = "<p class='error'>Errore: eliminazione non riuscita.</p>";
            }
        } catch (Exception $e) {
            header("location: ../pages/500.html");
            exit();
        }
    }
}

if (isset($_GET['status']) && $_GET['status'] === 'ok') {
    $messaggio_successo_aggiunta_commento = "<p class='success' aria-live='polite'>Commento pubblicato!</p>";
}

// 4. RECUPERO DATI PER RENDERING
try {
    $db_connection = new DBConnection();
    $gara_data = $db_connection->get_gara_data($id_gara_attuale);
    $commenti_data = $db_connection->get_commenti($id_gara_attuale);
    $db_connection->close_connection();
} catch (Exception $e) {
    header("location: ../pages/500.html");
    exit();
}

if (!$gara_data) {
    header("location: gare.php");
    exit();
}

$format_driver = function($nome, $cognome, $nazionalita) {
    $full_name = htmlspecialchars($nome . " " . $cognome);
    if ($nazionalita && $nazionalita !== 'it') {
        return '<span lang="' . htmlspecialchars($nazionalita) . '">' . $full_name . '</span>';
    }
    return $full_name;
};

$html_page = file_get_contents("../pages/commenti.html");

// 5. COSTRUZIONE INTERFACCIA DETTAGLI GARA
$data_val = $gara_data['data'];
$data_it = date("d/m/Y", strtotime($data_val));
$nazione = htmlspecialchars($gara_data['circuito_nazione']);
$anno = date("Y", strtotime($data_val));
$titolo_gp = $nazione . ' <span lang="en">Grand Prix</span> ' . $anno;
$titolo_gp_no_span = $nazione . " " .  $anno;

$p1_display = $format_driver($gara_data['p1_nome'], $gara_data['p1_cognome'], $gara_data['p1_nazionalita']);
$p2_display = $format_driver($gara_data['p2_nome'], $gara_data['p2_cognome'], $gara_data['p2_nazionalita']);
$p3_display = $format_driver($gara_data['p3_nome'], $gara_data['p3_cognome'], $gara_data['p3_nazionalita']);

$info_gara_html = "
    <h3 class='titolo-commento'>" . htmlspecialchars($gara_data['circuito_nome']) . "</h3>
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
            <span class='driver'>$p1_display</span>
        </li>
        <li class='podium-item silver'>
            <span class='rank' aria-hidden='true'>2</span>
            <span class='driver'>$p2_display</span>
        </li>
        <li class='podium-item bronze'>
            <span class='rank' aria-hidden='true'>3</span>
            <span class='driver'>$p3_display</span>
        </li>
    </ol>";

// 6. COSTRUZIONE FORM COMMENTI
$form_commento = "";
if (isset($_SESSION["user"])) {
    $form_commento = '
        <h3 class="aggiungi-commento-titolo">Aggiungi un Commento</h3>
        <form id="form-commento" action="commenti.php" method="post">
            <input type="hidden" name="gara_id" value="' . $id_gara_attuale . '">
            <label for="testo-commento">Stai commentando come: <strong>' . htmlspecialchars($_SESSION["user"]) . '</strong></label>
            <textarea id="testo-commento" name="testo_commento" rows="4" required aria-required="true"></textarea>
            <button type="submit" name="invia_commento">Pubblica Commento</button>
        </form>
        ' . $err_aggiungi_commenti . '
        ' . $messaggio_successo_aggiunta_commento . '';
} else {
    $form_commento = '
        <section class="avviso-login">
            <p><a href="login.php">Accedi</a> per commentare.</p>
        </section>';
}

// 7. GENERAZIONE LISTA COMMENTI
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
        $id_commento = $comm['id'];
        $bottone_elimina = "";

        if (isset($_SESSION['user']) && $_SESSION['user'] == $utente) {
            $bottone_elimina = "
                <form action='commenti.php' method='POST'>
                    <input type='hidden' name='comment_id' value='$id_commento'>
                    <input type='hidden' name='gara_id' value='$id_gara_attuale'>
                    <button type='submit' class='btn-delete'>Elimina</button>
                </form>";
        }

        $commenti_html .= "<li>
            <article class='commento-card'>
                <header>
                    <div class='comment-meta'>
                        <h3>Commento di: $utente</h3>
                        <p class='comment-date'>Pubblicato il <time datetime='$data_iso'>$data_it</time> alle $ora_it</p>
                    </div>
                    $bottone_elimina
                </header>
                <p class='comment-content'>$testo</p>
            </article>
        </li>";
    }
}

// 8. SOSTITUZIONI FINALI E OUTPUT
$html_page = str_replace("[form-commento]", $form_commento, $html_page);
$html_page = str_replace("[dettagli-gara]", $info_gara_html, $html_page);
$html_page = str_replace("[titolo-gp]", $titolo_gp, $html_page);
$html_page = str_replace("[titolo-gp-no-span]", $titolo_gp_no_span, $html_page);
$html_page = str_replace("[lista-commenti]", $commenti_html, $html_page);
$html_page = str_replace("[err-eliminazione]", $err_eliminazione, $html_page);

echo $html_page;
?>