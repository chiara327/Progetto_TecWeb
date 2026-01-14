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
			return false;
		}

        return true;
    }

    public function register_new_user($username, $password, $nome, $cognome, $dataNascita) {
        if (!$this->check_for_existing_username($username)) {
            return false;
        }

		$query = "INSERT INTO Utente (username, password, nome, cognome, dataNascita) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->connection->prepare($query);
        // Check errore strano (vedi Luzzauto)

        $stmt->bind_param("sssss", $username, $password, $nome, $cognome, $dataNascita);
        $result = $stmt->execute();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    // Area Utente functions
    public function get_user_comments($username) {
        $query = "SELECT testo, data FROM Commento WHERE username = ? ORDER BY data DESC LIMIT 5";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $username);
    
        if (!$stmt->execute()) {
            return []; // Ritorna un array vuoto in caso di errore
        }
    
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function get_user_info($username) {
        $query = "SELECT nome, cognome, dataNascita FROM Utente WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $username);
    
        if (!$stmt->execute()) {
            return null;
        }
    
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function update_user_info($username, $nome, $cognome, $dataNascita) {
        $query = "UPDATE Utente SET nome = ?, cognome = ?, dataNascita = ? WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssss", $nome, $cognome, $dataNascita, $username);
        return $stmt->execute();
    }

    public function verify_password($username, $password) {
        $query = "SELECT password FROM Utente WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return password_verify($password, $row['password']);
        }
        return false;
    }

    public function update_username($old_username, $new_username) {
        // Verifica prima che il nuovo username non sia già preso
        if (!$this->check_for_existing_username($new_username)) {
            return false;
        }
        $query = "UPDATE Utente SET username = ? WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $new_username, $old_username);
        return $stmt->execute();
    }
    // ---------------------------------------------

    public function get_scuderia($nome) {
        $query = "SELECT * FROM Scuderie WHERE nome = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $nome);

        if (!$stmt->execute()) {
            die("Errore durante l'esecuzione: " . $stmt->error);
        }

        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function get_pilota($id) {
        $query = "SELECT * FROM Piloti WHERE id = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            die("Errore durante l'esecuzione: " . $stmt->error);
        }

        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
}
?>