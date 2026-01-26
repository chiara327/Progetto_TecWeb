<?php
require_once 'db_connection.php';
use DB\DBConnection;

$html_page = file_get_contents("../pages/gare.html");

$db_connection = new DBConnection();
$gare_data = $db_connection->get_gare_data();
$db_connection->close_connection();

$gare_html = "";

if (empty($gare_data)) {
    $gare_html = "<p>Nessuna gara registrata nel database.</p>";
} else {
    // Array dei mesi in italiano
    $mesi = array(1=>'Gennaio',2=>'Febbraio',3=>'Marzo',4=>'Aprile',5=>'Maggio',6=>'Giugno',7=>'Luglio',8=>'Agosto',9=>'Settembre',10=>'Ottobre',11=>'Novembre',12=>'Dicembre');
    
    foreach ($gare_data as $g) {
        // Formattazione data (es: 07 Settembre 2025)
        $timestamp = strtotime($g['data']);
        $giorno = date('d', $timestamp);
        $mese_num = date('n', $timestamp);
        $anno = date('Y', $timestamp);
        $data_formattata = $giorno . ' ' . $mesi[$mese_num] . ' ' . $anno;
        $data_iso = date("Y-m-d", $timestamp);
        
        // Prepariamo un ID unico per l'accessibilità (aria-labelledby)
        $card_id = "gp-" . $g['id'];
        
        // Immagine: usiamo il nome del circuito o della città (pulito da spazi)
        $img_name = strtolower(str_replace(' ', '_', $g['circuito_citta']));

        $gare_html .= "
            <section class=\"gp\" aria-labelledby=\"$card_id\">
                <img src=\"../resources/gare/{$img_name}.jpg\" alt=\"Immagine del Gran Premio di {$g['circuito_citta']}\" aria-hidden=\"true\"> 
                <div>
                    <h2 id=\"$card_id\">
                        {$g['circuito_citta']} <span lang=\"en\">Grand Prix</span>
                    </h2>
                    <p><strong><time datetime=\"$data_iso\">$data_formattata</time></strong></p>
                    <dl>
                        <dt>Circuito</dt>
                        <dd>{$g['circuito_nome']}, {$g['circuito_citta']}</dd>
                        <dt>Tipo</dt>
                        <dd>Circuito permanente</dd>
                        <dt>1° classificato</dt>
                        <dd>
                            <a href=\"informazioni_pilota.php?id={$g['p1_id']}\" aria-label=\"Profilo di {$g['p1_nome']} {$g['p1_cognome']}\">
                                {$g['p1_nome']} {$g['p1_cognome']}
                            </a>
                        </dd>
                        <dt>2° classificato</dt>
                        <dd><a href=\"informazioni_pilota.php?id={$g['p2_id']}\">{$g['p2_nome']} {$g['p2_cognome']}</a></dd>
                        <dt>3° classificato</dt>
                        <dd><a href=\"informazioni_pilota.php?id={$g['p3_id']}\">{$g['p3_nome']} {$g['p3_cognome']}</a></dd>
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