<?php
require_once "db_connection.php";
use DB\DBConnection;

$html_page = file_get_contents("../pages/circuiti.html");
$dynamic_content = "";

$db_connection = new DBConnection();
$circuiti_data = $db_connection->get_circuiti_page_data();
$db_connection->close_connection();

function render_circuits($id, $nome, $citta, $nazione, $lunghezza, $curve) {
    $display_name = htmlspecialchars($nome);
    $display_city = htmlspecialchars($citta);
    $display_country = htmlspecialchars($nazione);

    $display_length = htmlspecialchars($lunghezza); 
    $display_corners = htmlspecialchars($curve);

    $img_name = strtolower(str_replace(' ', '_', $nome));

    return  
    <<<HTML
        <article class="circuiti">
            <img src="../resources/circuiti/{$img_name}.svg" 
                alt="Mappa del circuito {$display_name}, situato a {$display_city}, {$display_country}, lungo {$display_length} metri con {$display_corners} curve." >
                 
            <div class="circuiti-info">
                <h2>{$display_name}</h2>
                <dl>
                    <dt>Luogo</dt>
                    <dd>{$display_city}, {$display_country}</dd>
                    <dt>Lunghezza</dt>
                    <dd>{$display_length} m</dd>
                    <dt>Curve</dt>
                    <dd>{$display_corners}</dd>
                </dl>
            </div>
            </div>
        </article>
    HTML;
}

foreach ($circuiti_data as $circuito) {
    $dynamic_content .= render_circuits(
        $circuito["id"],
        $circuito["nome"],
        $circuito["citta"],
        $circuito["nazione"],
        $circuito["lunghezza"], 
        $circuito["numero_curve"]      
    );
}

$html_page = str_replace("[circuiti_dinamici]", $dynamic_content, $html_page);
echo $html_page;
?>