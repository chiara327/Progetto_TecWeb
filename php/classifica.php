<?php
require_once 'db_connection.php';
use DB\DBConnection;

// Recupero il template HTML
$html_page = file_get_contents("../pages/classifica.html");

// --- RECUPERO DATI DAL DB ---
try {
    $db_connection = new DBConnection();
    $piloti_data = $db_connection->get_drivers_standings();
    $costruttori_data = $db_connection->get_constructors_standings();
    $db_connection->close_connection();
} catch (Exception $e) {
    header("location: ../pages/500.html");
    exit();
}

// --- GENERAZIONE HTML CLASSIFICA PILOTI ---
$piloti_html = "";
if (empty($piloti_data)) {
    $piloti_html = "<tr><td colspan='4'>Dati non disponibili.</td></tr>";
} else {
    $pos = 1;
    foreach ($piloti_data as $p) {
        $piloti_html .= "<tr>
            <th scope='row'>" . $pos . "</th>
            <td>" . htmlspecialchars($p['nome'] . " " . $p['cognome']) . "</td>
            <td>" . htmlspecialchars($p['nome_scuderia'] ?? 'N/D') . "</td>
            <td>" . $p['punti'] . "</td>
        </tr>";
        $pos++;
    }
}

// --- GENERAZIONE HTML CLASSIFICA COSTRUTTORI ---
$costruttori_html = "";
if (empty($costruttori_data)) {
    $costruttori_html = "<tr><td colspan='3'>Dati non disponibili.</td></tr>";
} else {
    $pos = 1;
    foreach ($costruttori_data as $c) {
        $costruttori_html .= "<tr>
            <th scope='row'>" . $pos . "</th>
            <td>" . htmlspecialchars($c['nome_scuderia']) . "</td>
            <td>" . $c['punti'] . "</td>
        </tr>";
        $pos++;
    }
}

// --- SOSTITUZIONE SEGNAPOSTO ---
$html_page = str_replace("[classifica-piloti]", $piloti_html, $html_page);
$html_page = str_replace("[classifica-costruttori]", $costruttori_html, $html_page);

// Output finale
echo $html_page;
?>