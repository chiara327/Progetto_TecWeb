<?php
require_once "db_connection.php";
use DB\DBConnection;
session_start();

$html_page = file_get_contents("../pages/scuderie.html");
$dynamic_content = "";

function create_slug($string) {
    if (!$string) return "";

    $string = strtolower($string);

    $string = str_replace(' ', '', $string);

    $string = preg_replace('/[^a-z0-9]/', '', $string);
    return $string;
}

$db_connection = new DBConnection();
$scuderie_data = $db_connection->get_scuderie_page_data();
$db_connection->close_connection();

foreach ($scuderie_data as $team) {
    $nome_team = htmlspecialchars($team["team_name"]);
    $slug_team = create_slug($nome_team);
    $team_straniero = (stripos($nome_team, 'Ferrari') === false && stripos($nome_team, 'Unipd') === false);

    $team_alt = "<a href=\"../php/informazioni_scuderia.php?team={$nome_team}\">
                    <img src=\"../resources/{$slug_team}.jpg\" alt=\"Logo della scuderia {$nome_team}\">
                    <p id=\"{$slug_team}-desc\" class=\"visually-hidden\">
                    Logo della scuderia {$nome_team}.
                    </p>
                </a>";

    $p1_id = $team["p1_id"];
    $p2_id = $team["p2_id"];
    $pilota1_name = htmlspecialchars($team["p1_nome"] . " " . $team["p1_cognome"]);
    $pilota2_name = htmlspecialchars($team["p2_nome"] . " " . $team["p2_cognome"]);
    $team_header = $team_straniero ? "<span lang=\"en\">{$nome_team}</span>" : $nome_team;

    $dynamic_content .= 
    <<<HTML
        <article aria-labelledby="{$slug_team}-heading" class="team">
            <h2 id="{$slug_team}-heading">{$team_header}</h2>
            {$team_alt}
            <h3>Piloti</h3>
            <ul>
                <li>
                    <a href="informazioni_pilota.php?id={$p1_id}">{$pilota1_name}</a>
                </li>
                <li>
                    <a href="informazioni_pilota.php?id={$p2_id}">{$pilota2_name}</a>
                </li>
            </ul>    
        </article>
    HTML;
}

$html_page = str_replace("[scuderie_dinamiche]", $dynamic_content, $html_page);
echo $html_page;
?>