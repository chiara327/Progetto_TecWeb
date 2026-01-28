<?php
require_once 'db_connection.php';
use DB\DBConnection;

session_start();

$html_page = file_get_contents("../pages/piloti.html");
$dynamic_content = "";


function render_drivers($id, $nome, $cognome) {
    $display_name = htmlspecialchars($nome . " " . $cognome);

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
            <article>
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
    $piloti_data = $db_connection->get_piloti_page_data();
    $db_connection->close_connection();
} catch (Exception $e) {
    header("location: ../pages/500.html");
    exit();
}

foreach ($piloti_data as $team) {
    $nome_team = htmlspecialchars($team["team_name"]);
    $slug_team = create_slug($nome_team);
    $team_straniero = (stripos($nome_team, 'Ferrari') === false && stripos($nome_team, 'Unipd') === false);

    $team_header = $team_straniero ? "<span lang=\"en\">{$nome_team}</span>" : $nome_team;

    $driver1_html = render_drivers($team["p1_id"], $team["p1_nome"], $team["p1_cognome"]);
    $driver2_html = render_drivers($team["p2_id"], $team["p2_nome"], $team["p2_cognome"]);

    $dynamic_content .= 
    <<<HTML
        <section aria-labelledby="{$slug_team}-heading" class="piloti">
            <h3 id="{$slug_team}-heading">
                $team_header
            </h3>
            <ul>
                $driver1_html
                $driver2_html
            </ul>
        </section>
    HTML;
}

$html_page = str_replace("[piloti_dinamici]", $dynamic_content, $html_page);
echo $html_page;
?>