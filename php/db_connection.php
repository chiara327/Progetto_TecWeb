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

    private const HOST = "localhost";
	private const DB_NAME = "";
	private const USER = "";
	private const PASSWORD = "";

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
        $result = $stmt->execute();

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }


}
?>