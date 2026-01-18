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

    return  
    <<<HTML
        <article class="circuit-card">
            <img src="../resources/circuito_{$id}.jpg" 
                 alt="Mappa del circuto {$display_name}" 
                 class="circuit-image">
                 
            <div class="circuit-info">
                <h3 class="circuit-name">{$display_name}</h3>
                
                <div class="circuit-location">
                    <span>{$display_city}, <span class="nation">{$display_country}</span></span>
                </div>

                <div class="circuit-stats">
                    <div class="stat-item">
                        <span class="stat-label">Lunghezza</span>
                        <span class="stat-value">{$display_length} m</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Curve</span>
                        <span class="stat-value">{$display_corners}</span>
                    </div>
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