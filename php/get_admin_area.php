<?php
require_once 'db_connection.php';
use DB\DBConnection;

session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit("Accesso negato");
}


if (isset($_POST['conferma_creazione_utente'])) {

    //chiama check_invalid_input qui se vuoi validare i campi
    //se trova errori, deve fare restore degli altri input e heading su se stesso.
    //RICORDA DI RISELEZIONARE I RADIO BUTTON NELLA FUNZIONE DI RESTORE

    $username = $_POST['usernameAdd'];
    $password = $_POST['passwordAdd']; 
    $admin = $_POST['adminPowerAdd'];
    $nome = $_POST['nomeAdd'];
    $cognome = $_POST['cognomeAdd'];
    $nascita = $_POST['dataNascitaAdd'];

    $dbconnection = new DBConnection();
    $result = $dbconnection->admin_add_user($username, password_hash($password, PASSWORD_BCRYPT), $admin, $nome, $cognome, $nascita);
    $dbconnection->close_connection();

    header("location: area_amministratore.php");
}

if (isset($_POST['conferma_creazione_gare'])) { //id circuito_id data primo_posto secondo_posto terzo_posto

    $id = $_POST['id'];
    $circuito_id = $_POST['circuito_id']; 
    $data = $_POST['data'];
    $primo_posto = $_POST['primo_posto'];
    $secondo_posto = $_POST['secondo_posto'];
    $terzo_posto = $_POST['terzo_posto'];

    $dbconnection = new DBConnection();
    $result = $dbconnection->admin_add_race($id, $circuito_id, $data, $primo_posto, $secondo_posto, $terzo_posto);
    $dbconnection->close_connection();

    header("location: area_amministratore.php");
}

$area = $_POST['area'] ?? null;

if (!$area) {
    exit("");
}

$action = $_POST['azione'] ?? null;

if (!$action) {
    exit("");
}

if($action == 'aggiungi'){
    switch ($area) {
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
            <form class="admin-form" action="get_admin_area.php#ConfermaCreazioneGare" method="post" id="form-gare">

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

                <button type="submit" name="conferma_creazione_gare" id="ConfermaCreazioneGare">
                    Aggiungi gara
                </button>
            </form>';
            break;
        case 'utenti':
            echo '
            <form class="admin-form" action="get_admin_area.php#ConfermaCreazioneUtente" method="post" id="form-utenti">

                <label for="usernameAdd">Username</label>
                <input type="text" name="usernameAdd" required>

                <label for="passwordAdd">Password</label>
                <input type="password" name="passwordAdd" required>

                <label for="adminPower">Admin</label>
                <select name="adminPowerAdd">
                    <option value="0">No</option>
                    <option value="1">Si</option>
                </select>

                <label for="nomeAdd">Nome</label>
                <input type="text" name="nomeAdd" required>

                <label for="cognomeAdd">Cognome</label>
                <input type="text" name="cognomeAdd" required>

                <label for="dataNascitaAdd">Data di nascita</label>
                <input type="date" name="dataNascitaAdd" required>

                <button type="submit" name="conferma_creazione_utente" id="ConfermaCreazioneUtente">
                    Crea utente
                </button>
            </form>';
            break;
        default:
            echo "<p>Area non ancora implementata.</p>";
    }
}
if($action == 'modifica'){
    switch ($area) {
        case 'classifica_costruttori':
            echo "<p>Inserisci la scuderia da modificare dalla classifica nel relativo anno</p>";
            echo '
            <form class="admin-form">
                <label for="anno">Anno</label>
                <input type="number" id="anno" name="anno" min="1950" max="2100" required>

                <label for="scuderia_nome">Scuderia</label>
                <input type="text" id="scuderia_nome" name="scuderia_nome" required>
                <button type="submit">Cerca</button>
            </form>';
            break;
        case 'classifica_piloti':
            echo "<p>Inserisci il pilota da modificare dalla classifica nel relativo anno</p>";
            echo '
            <form class="admin-form">
                <label for="id">ID</label>
                <input type="text" name="id" required>

                <label for="anno">Anno</label>
                <input type="number" name="anno" min="1950" required>
                <button type="submit">Cerca</button>
            </form>';
            break;
        case 'gare':
            echo "<p>Inserisci l'id della gara da modificare</p>";
            echo '
            <form class="admin-form">
                <label for="id">ID</label>
                <input type="text" name="id" required>
                <button type="submit">Cerca</button>
            </form>';
            break;
        case 'utenti':
            echo "<p>Inserisci lo username dell'utente da modificare</p>";
            echo '
            <form class="admin-form">
                <label for="username">Username</label>
                <input type="text" name="username" required>
                <button type="submit">Cerca</button>
            </form>';
            break;

        default:
            echo "<p>Area non ancora implementata.</p>";
    }
}
if($action == 'elimina'){
    switch ($area) {
        case 'classifica_costruttori':
            echo "<p>Inserisci la scuderia da eliminare dalla classifica nel relativo anno</p>";
            echo '
            <form class="admin-form">
                <label for="anno">Anno</label>
                <input type="number" id="anno" name="anno" min="1950" max="2100" required>

                <label for="scuderia_nome">Scuderia</label>
                <input type="text" id="scuderia_nome" name="scuderia_nome" required>
                <button type="submit">Conferma Elimina</button>
            </form>';
            break;
        case 'classifica_piloti':
            echo "<p>Inserisci il pilota da eliminare dalla classifica nel relativo anno</p>";
            echo '
            <form class="admin-form">
                <label for="id">ID</label>
                <input type="text" name="id" required>

                <label for="anno">Anno</label>
                <input type="number" name="anno" min="1950" required>
                <button type="submit">Conferma Elimina</button>
            </form>';
            break;
        case 'gare':
            echo "<p>Inserisci l'id della gara da eliminare</p>";
            echo '
            <form class="admin-form">
                <label for="id">ID</label>
                <input type="text" name="id" required>
                <button type="submit">Conferma Elimina</button>
            </form>';
            break;
        case 'utenti':
            echo "<p>Inserisci lo username dell'utente da eliminare</p>";
            echo '
            <form class="admin-form">
                <label for="username">Username</label>
                <input type="text" name="username" required>
                <button type="submit">Conferma Elimina</button>
            </form>';
            break;

        default:
            echo "<p>Area non ancora implementata.</p>";
    }
}


