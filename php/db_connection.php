<?php

namespace DB;
use Exception;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class DBConnection {
    /*
        HOST: localhost per il server tecweb
        NAME: username del laboratorio
        USER: username del laboratorio
        PASS: password nel file .txt del server personale
    */

    private const HOST = "db";
	private const DB_NAME = "f1_db";
	private const USER = "root";
	private const PASSWORD = "root_password";

    private $connection;

    public function __construct() {
		try {
			$this->connection = mysqli_connect(self::HOST, self::USER, self::PASSWORD, self::DB_NAME);
		} catch (Exception $e) {
			throw $e;
		}
    }

    public function get_connection() {
		if (!$this->connection->connect_errno) {
			return $this->connection;
        }
	}

	public function close_connection() {
		if (!$this->connection->connect_errno) {
			$this->connection->close();
        }
	}

    public function check_for_existing_username($username) {
        $query = "SELECT * FROM Utente WHERE username = ?";

        $stmt = $this->connection->prepare($query);
        // Check errore strano (vedi Luzzauto)

        $stmt->bind_param("s", $username);

        if (!$stmt->execute()) {
            die ("Errore riscontrato durante l'esecuzione: " . $stmt->error);
        }

        $result = $stmt->get_result();
		$rows = $result->fetch_all(MYSQLI_ASSOC);
		$numRows = count($rows);

		if($numRows != 0){
			return -1;
		}

        return 0;
    }

    public function register_new_user($username, $password, $nome, $cognome, $dataNascita) {
        if ($this->check_for_existing_username($username) === -1) {
            return -1;
        }

		$query = "INSERT INTO Utente (username, password, nome, cognome, dataNascita) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->connection->prepare($query);
        // Check errore strano (vedi Luzzauto)

        $stmt->bind_param("sssss", $username, $password, $nome, $cognome, $dataNascita);
        // TODO: Check return value di execute in un if
        $result = $stmt->execute();

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

    public function login_user($username, $password) {
        $query = "SELECT username, password FROM Utene WHERE username = ?";

        $stmt = $this->connection->prepare($query);

        $stmt->bind_param("s", $username);

        // TODO: Check return value di execute in un if
        $result = $stmt->execute();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        
        if (count($rows) == 0) {
            return -1;
        } else {
            // TODO: Fare hash password nella registrazione e qui fare controllo hash
            return 1;
        }

    }

    // TODO: Controllare se le 3 funzioni successive sono OK
    public function get_user_info($username) {
        $query = "SELECT username, nome, cognome, dataNascita FROM Utente WHERE username = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $username);

        if (!$stmt->execute()) {
            die("Errore durante l'esecuzione: " . $stmt->error);
        }

        $result = $stmt->get_result();
        
        return $result->fetch_assoc(); 
    }

    public function get_user_comments($username) {
        // Nota: Assicurati che il nome della tabella e delle colonne 
        // corrispondano a quelle del tuo database (es. Commento o commenti)
        $query = "SELECT testo, data FROM Commento WHERE username = ? ORDER BY data DESC LIMIT 5";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $username);

        if (!$stmt->execute()) {
            die("Errore durante l'esecuzione: " . $stmt->error);
        }

        $result = $stmt->get_result();
        // Restituiamo tutte le righe trovate come array associativo
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function update_user_profile($username, $nome, $cognome, $dataNascita) {
        $query = "UPDATE Utente SET nome = ?, cognome = ?, dataNascita = ? WHERE username = ?";

        $stmt = $this->connection->prepare($query);
        
        // "ssss" indica che passiamo 4 stringhe
        $stmt->bind_param("ssss", $nome, $cognome, $dataNascita, $username);

        if (!$stmt->execute()) {
            die("Errore durante l'aggiornamento: " . $stmt->error);
        }

        // Ritorna true se l'operazione è andata a buon fine
        return true; 
    }
}
?>