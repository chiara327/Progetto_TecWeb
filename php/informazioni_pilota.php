<?php
require_once 'db_connection.php';

use DB\DBConnection;

$idSlug = filter_input(INPUT_GET, 'id');

if (!$idSlug) {
    header('Location: ../pages/piloti.html');
    exit;
}

$html_page = file_get_contents("../pages/informazioni_pilota.html");

$pilota = "";

try {
    $db_connection = new DBConnection();
    $pilota = $db_connection->get_pilota($idSlug);
    $db_connection->close_connection();
} catch (Exception) {
    header("location: ../pages/500.html");
    exit();
}

$sostituzioni = [
    "[nome]" => htmlspecialchars($pilota['nome']),
    "[cognome]" => htmlspecialchars($pilota['cognome']),
    "[numero]" => htmlspecialchars($pilota['numero']),
    "[eta]" => htmlspecialchars($pilota['eta']),
    "[vittorie]" => htmlspecialchars($pilota['vittorie']),
    "[n_pole]" => htmlspecialchars($pilota['n_pole']),
    "[gran_premi]" => htmlspecialchars($pilota['gran_premi']),
    "[titoli_mondiali]" => htmlspecialchars($pilota['titoli_mondiali']),
    "[Pilota]" => htmlspecialchars($pilota['nome']) . " " . htmlspecialchars($pilota['cognome']),
];

foreach ($sostituzioni as $placeholder => $valore) {
    $html_page = str_replace($placeholder, $valore, $html_page);
}

echo $html_page;
?>