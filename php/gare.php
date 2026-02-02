<?php
require_once 'db_connection.php';
use DB\DBConnection;

$html_page = file_get_contents("../pages/gare.html");

try {
    $db_connection = new DBConnection();
    $gare_data = $db_connection->get_gare_data();
    $db_connection->close_connection();
} catch (Exception $e) {
    echo $e;
    header("location: ../pages/500.html");
    exit();
}

$gare_html = "";

if (empty($gare_data)) {
    $gare_html = "<p>Nessuna gara registrata nel database.</p>";
} else {
    $mesi = array(1=>'Gennaio', 2=>'Febbraio', 3=>'Marzo', 4=>'Aprile',
                  5=>'Maggio', 6=>'Giugno', 7=>'Luglio', 8=>'Agosto',
                  9=>'Settembre',10=>'Ottobre',11=>'Novembre',12=>'Dicembre');
    
    foreach ($gare_data as $g) {
        $timestamp = strtotime($g['data']);
        $giorno = date('d', $timestamp);
        $mese_num = date('n', $timestamp);
        $anno = date('Y', $timestamp);
        $data_formattata = $giorno . ' ' . $mesi[$mese_num] . ' ' . $anno;
        $data_iso = date("Y-m-d", $timestamp);
        $card_id = "gp-" . $g['id'];
        $img_name = strtolower(str_replace(' ', '_', $g['circuito_citta']));

        $format_pilot = function($nome, $cognome, $nazionalita) {
            $full_name = htmlspecialchars($nome . " " . $cognome);
            if ($nazionalita && $nazionalita !== 'it') {
                return '<span lang="' . htmlspecialchars($nazionalita) . '">' . $full_name . '</span>';
            }
            return $full_name;
        };

        $p1_display = $format_pilot($g['p1_nome'], $g['p1_cognome'], $g['p1_nazionalita']);
        $p2_display = $format_pilot($g['p2_nome'], $g['p2_cognome'], $g['p2_nazionalita']);
        $p3_display = $format_pilot($g['p3_nome'], $g['p3_cognome'], $g['p3_nazionalita']);

        $gare_html .= "
            <section class=\"gp\" aria-labelledby=\"$card_id\">
                <img src=\"../resources/gare/{$img_name}.jpg\" alt=\"Gran Premio di {$g['circuito_citta']}\" aria-hidden=\"true\"> 
                <div>
                    <h2 id=\"$card_id\">
                        {$g['circuito_citta']} <span lang=\"en\">Grand Prix</span>
                    </h2>
                    <p><strong><time datetime=\"$data_iso\">$data_formattata</time></strong></p>
                    <dl>
                        <dt>Circuito</dt>
                        <dd>{$g['circuito_nome']}, {$g['circuito_citta']}</dd>
                        <dt>Tipo</dt>
                        <dd>{$g['circuito_tipo']}</dd>
                        <dt>1° classificato</dt>
                        <dd>
                            <a href=\"informazioni_pilota.php?id={$g['p1_id']}\" aria-label=\"Profilo di {$g['p1_nome']} {$g['p1_cognome']}\">
                                $p1_display
                            </a>
                        </dd>
                        <dt>2° classificato</dt>
                        <dd><a href=\"informazioni_pilota.php?id={$g['p2_id']}\">$p2_display</a></dd>
                        <dt>3° classificato</dt>
                        <dd><a href=\"informazioni_pilota.php?id={$g['p3_id']}\">$p3_display</a></dd>
                    </dl>
                    <form action=\"commenti.php\" method=\"POST\" class=\"form-commenti-link\">
                        <input type=\"hidden\" name=\"gara_id\" value=\"{$g['id']}\">
                        
                        <button type=\"submit\" class=\"btn-view-comments\">
                            Vedi commenti <span class=\"sr-only\">per il GP di {$g['circuito_citta']}</span>
                        </button>
                    </form>
                </div>
            </section>";
    }
}

$html_page = str_replace("[lista-gare]", $gare_html, $html_page);
echo $html_page;
?>