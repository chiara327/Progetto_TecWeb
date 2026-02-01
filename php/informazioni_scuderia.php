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

function render_drivers($id, $nome, $cognome, $scuderia) {
    $display_name =  htmlspecialchars($nome . " " . $cognome);

    $filename = strtolower($nome . "_" . $cognome . ".jpg");
    $base_path = "../resources/piloti/";
    $full_path = __DIR__ . "/" . $base_path . $filename; 

    if (!file_exists($full_path)) {
        $img_src = $base_path . "placeholder_driver.jpg";
    } else {
        $img_src = $base_path . $filename;
    }

    return <<<HTML
        <li>
            <article class="{$scuderia}-heading">
                <a href="informazioni_pilota.php?id={$id}">
                    <img src="{$img_src}" alt="Ritratto di {$display_name}">
                    <h3>{$display_name}</h3>
                </a>
            </article>
        </li>
    HTML;
}

function create_slug($string) {
    if (!$string) return "";

    $string = strtolower($string);

    $string = str_replace(' ', '', $string);

    $string = preg_replace('/[^a-z0-9]/', '', $string);
    return $string;
}

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
    "[nome_scuderia]"     => (stripos($scuderia["nome"], 'Ferrari') === true ? htmlspecialchars($scuderia['nome']) : "<span lang=\"en\">" . htmlspecialchars($scuderia['nome']) . "</span>"),
    "[Scuderia]"          => htmlspecialchars($scuderia['nome']),
    "[presenze]"          => htmlspecialchars($scuderia['presenze']),
    "[punti_campionato]"  => htmlspecialchars($scuderia['punti_campionato']),
    "[titoli]"            => htmlspecialchars($scuderia['titoli']), 
    "[pilota1_nome]"      => render_drivers($pilota1["id"], $pilota1["nome"], $pilota1["cognome"], create_slug($scuderia["nome"])),
    "[pilota2_nome]"      => render_drivers($pilota2["id"], $pilota2["nome"], $pilota2["cognome"], create_slug($scuderia["nome"]))
];

foreach ($sostituzioni as $placeholder => $valore) {
    $html_page = str_replace($placeholder, $valore, $html_page);
}

echo $html_page;
?>