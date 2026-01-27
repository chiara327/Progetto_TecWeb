<?php
require_once 'db_connection.php';
use DB\DBConnection;

$teamSlug = filter_input(INPUT_GET, 'team');

if (!$teamSlug) {
  header('Location: ../pages/scuderie.html');
  exit;
}

$html_page = file_get_contents("../pages/informazioni_scuderia.html");

$scuderia = "";

try {
    $db_connection = new DBConnection();
    $scuderia = $db_connection->get_scuderia($teamSlug);
    $pilota1 = $db_connection->get_pilota($scuderia["pilota_attuale1_id"]);
    $pilota2 = $db_connection->get_pilota($scuderia["pilota_attuale2_id"]);
    $db_connection->close_connection();
} catch (Exception) {
    header("location: ../pages/500.html");
}

$sostituzioni = [
    "[nome_scuderia]"     => htmlspecialchars($scuderia['nome']),
    "[Scuderia]"          => htmlspecialchars($scuderia['nome']),
    "[presenze]"          => htmlspecialchars($scuderia['presenze']),
    "[punti_campionato]"  => htmlspecialchars($scuderia['punti_campionato']),
    "[titoli]"            => htmlspecialchars($scuderia['titoli']), 
    "[pilota1_nome]"      => "<a href='informazioni_pilota.php?id=" . $scuderia["pilota_attuale1_id"] . "'>" . htmlspecialchars($pilota1["nome"]) . " " . htmlspecialchars($pilota1["cognome"]) . "</a>",
    "[pilota2_nome]"      => "<a href='informazioni_pilota.php?id=" . $scuderia["pilota_attuale2_id"] . "'>" . htmlspecialchars($pilota2["nome"]) . " " . htmlspecialchars($pilota2["cognome"]) . "</a>",
];

foreach ($sostituzioni as $placeholder => $valore) {
    $html_page = str_replace($placeholder, $valore, $html_page);
}

echo $html_page;
?>