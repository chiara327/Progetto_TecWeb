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
    case 'classifica_costruttori':
        echo '
        <form class="admin-form" id="form-classifica-costruttori">

            <label for="anno">Anno</label>
            <input type="number" id="anno" name="anno" min="1950" max="2100" required>

            <label for="scuderia_nome">Scuderia</label>
            <input type="text" id="scuderia_nome" name="scuderia_nome" required>

            <label for="posizione">Posizione</label>
            <input type="number" id="posizione" name="posizione" min="1" required>

            <label for="punti">Punti</label>
            <input type="number" id="punti" name="punti" step="0.1" min="0" required>

            <button type="submit">
                Aggiungi classifica costruttori
            </button>
        </form>';
        break;
    case 'classifica_piloti':
        echo '
        <form class="admin-form" id="form-classifica-piloti">

            <label for="id">ID</label>
            <input type="text" name="id" required>

            <label for="anno">Anno</label>
            <input type="number" name="anno" min="1950" required>

            <label for="pilota_id">Pilota ID</label>
            <input type="text" name="pilota_id" required>

            <label for="posizione">Posizione</label>
            <input type="number" name="posizione" min="1" required>

            <label for="punti">Punti</label>
            <input type="number" name="punti" step="0.1" min="0" required>

            <button type="submit">
                Aggiungi classifica piloti
            </button>
        </form>';
        break;
    case 'gare':
        echo '
        <form class="admin-form" id="form-gare">

            <label for="id">ID</label>
            <input type="text" name="id" required>

            <label for="circuito_id">Circuito ID</label>
            <input type="text" name="circuito_id" required>

            <label for="data">Data gara</label>
            <input type="date" name="data" required>

            <label for="primo_posto">Primo posto (Pilota ID)</label>
            <input type="text" name="primo_posto" required>

            <label for="secondo_posto">Secondo posto (Pilota ID)</label>
            <input type="text" name="secondo_posto" required>

            <label for="terzo_posto">Terzo posto (Pilota ID)</label>
            <input type="text" name="terzo_posto" required>

            <button type="submit">
                Aggiungi gara
            </button>
        </form>';
        break;
    case 'piloti':
        echo '
        <form class="admin-form" id="form-piloti">

            <label for="id">ID</label>
            <input type="text" name="id" required>

            <label for="nome">Nome</label>
            <input type="text" name="nome" required>

            <label for="cognome">Cognome</label>
            <input type="text" name="cognome" required>

            <label for="numero">Numero</label>
            <input type="number" name="numero" min="1" required>

            <label for="vittorie">Vittorie</label>
            <input type="number" name="vittorie" min="0" required>

            <label for="n_pole">Pole position</label>
            <input type="number" name="n_pole" min="0" required>

            <label for="gran_premi">Gran premi</label>
            <input type="number" name="gran_premi" min="0" required>

            <label for="titoli_mondiali">Titoli mondiali</label>
            <input type="number" name="titoli_mondiali" min="0" required>

            <label for="punti">Punti</label>
            <input type="number" name="punti" step="0.1" min="0" required>

            <label for="eta">Età</label>
            <input type="number" name="eta" min="16" required>

            <button type="submit">
                Aggiungi pilota
            </button>
        </form>';
        break;
    case 'scuderie':
        echo '
        <form class="admin-form" id="form-scuderie">

            <label for="nome">Nome scuderia</label>
            <input type="text" name="nome" required>

            <label for="presenze">Presenze</label>
            <input type="number" name="presenze" min="0" required>

            <label for="pilota_attuale1_id">Pilota attuale 1 (ID)</label>
            <input type="text" name="pilota_attuale1_id" required>

            <label for="pilota_attuale2_id">Pilota attuale 2 (ID)</label>
            <input type="text" name="pilota_attuale2_id" required>

            <label for="punti_campionato">Punti campionato</label>
            <input type="number" name="punti_campionato" step="0.1" min="0" required>

            <label for="titoli">Titoli</label>
            <input type="number" name="titoli" min="0" required>

            <button type="submit">
                Aggiungi scuderia
            </button>
        </form>';
        break;
    case 'utenti':
        echo '
        <form class="admin-form" id="form-utenti">

            <label for="username">Username</label>
            <input type="text" name="username" required>

            <label for="password">Password</label>
            <input type="password" name="password" required>

            <label for="adminPower">Admin</label>
            <select name="adminPower">
                <option value="0">No</option>
                <option value="1">Sì</option>
            </select>

            <label for="nome">Nome</label>
            <input type="text" name="nome" required>

            <label for="cognome">Cognome</label>
            <input type="text" name="cognome" required>

            <label for="dataNascita">Data di nascita</label>
            <input type="date" name="dataNascita" required>

            <button type="submit">
                Crea utente
            </button>
        </form>';
        break;

    default:
        echo "<p>Area non ancora implementata.</p>";
}
