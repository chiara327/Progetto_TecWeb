<?php
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit("Accesso negato");
}

$area = $_POST['area'] ?? null;

if (!$area) {
    exit("");
}

switch ($area) {

    case 'circuiti':
        echo '
        <form class="admin-form">

            <label for="id_circuito">ID</label>
            <input type="text" name="id_circuito">

            <label for="nome">Nome</label>
            <input type="text" name="nome">

            <label for="citta">Città</label>
            <input type="text" name="citta">

            <label for="nazione">Nazione</label>
            <input type="text" name="nazione">

            <label for="lunghezza">Lunghezza</label>
            <input type="text" name="lunghezza">

            <label for="numero_curve">Numero curve</label>
            <input type="number" name="numero_curve">

            <button type="submit">Aggiungi circuito</button>
        </form>';
        break;

    default:
        echo "<p>Area non ancora implementata.</p>";
}
