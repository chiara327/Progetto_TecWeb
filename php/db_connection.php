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
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

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

    public function register_new_user($username, $password, $nome, $cognome, $dataNascita, $adminPower = 0) {
        if (!$this->check_for_existing_username($username)) {
            return false;
        }

        if ($adminPower) {
            $query = "INSERT INTO Utente (username, password, adminPower, nome, cognome, dataNascita) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            if ($stmt === false) {
			    die("Errore nella preparazione dello statement: " . $this->connection->error);
		    }

            $stmt->bind_param("ssssss", $username, $password, $adminPower, $nome, $cognome, $dataNascita);
            $result = $stmt->execute();
        } else {
		    $query = "INSERT INTO Utente (username, password, nome, cognome, dataNascita) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            if ($stmt === false) {
			    die("Errore nella preparazione dello statement: " . $this->connection->error);
		    }

            $stmt->bind_param("sssss", $username, $password, $nome, $cognome, $dataNascita);
            $result = $stmt->execute();
        }

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function login_user($username, $password) {
        $query = "SELECT username, password, adminPower FROM Utente WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}
        $stmt->bind_param("s", $username);

        if (!$stmt->execute()) {
            die ("Errore riscontrato durante l'esecuzione: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        
        // Se l'array è vuoto, l'utente non esiste
        if (count($rows) == 0) {
            return [null, false]; // Restituiamo null per adminPower
        } else {
            // L'utente esiste, verifichiamo la password
            if (password_verify($password, $rows[0]["password"])) {
                return [$rows[0]["adminPower"], true];
            }
            return [$rows[0]["adminPower"], false];
        }
    }

    // Area Utente functions
    public function get_user_comments($username) {
        $query = "SELECT 
                    C.testo, 
                    C.data, 
                    Ci.nome AS nome_gara 
                  FROM Commento C 
                  LEFT JOIN Gare G ON C.gara_id = G.id 
                  LEFT JOIN Circuiti Ci ON G.circuito_id = Ci.id 
                  WHERE C.username = ? 
                  ORDER BY C.data DESC 
                  LIMIT 5";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
        
    public function get_user_info($username) {
        $query = "SELECT nome, cognome, dataNascita FROM Utente WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}
        $stmt->bind_param("s", $username);
    
        if (!$stmt->execute()) {
            return null;
        }
    
        $result = $stmt->get_result();
        if (!$stmt->execute()) {
            die("Errore durante l'esecuzione: " . $stmt->error);
        }
        return $result->fetch_assoc();
    }
    
    public function update_user_info($username, $nome, $cognome, $dataNascita) {
        $query = "UPDATE Utente SET nome = ?, cognome = ?, dataNascita = ? WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}
        $stmt->bind_param("ssss", $nome, $cognome, $dataNascita, $username);
        if (!$stmt->execute()) {
            die("Errore durante l'esecuzione: " . $stmt->error);
        }
        return $stmt->execute();
    }

    public function verify_password($username, $password) {
        $query = "SELECT password FROM Utente WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}
        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            die("Errore durante l'esecuzione: " . $stmt->error);
        }
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
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}
        $stmt->bind_param("ss", $new_username, $old_username);

        return $stmt->execute();
    }

    public function update_password($username, $hashed_password) {
        $query = "UPDATE Utente SET password = ? WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}
        $stmt->bind_param("ss", $hashed_password, $username);

        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function delete_user($username) {
        try {
            $query = "DELETE FROM Utente WHERE username = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("s", $username);
            return $stmt->execute();
        } catch (Exception $e) {
            // Se c'è un errore, annulla tutto (rollback)
            $this->connection->rollback();
            return false;
        }
    }
    // ---------------------------------------------

    public function get_scuderia($nome) {
        $query = "SELECT * FROM Scuderie WHERE nome = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $nome);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}
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
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}
        if (!$stmt->execute()) {
            die("Errore durante l'esecuzione: " . $stmt->error);
        }

        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public function get_piloti_page_data() {
        $query = "SELECT 
                    s.nome AS team_name,
                    p1.id AS p1_id,
                    p1.nome AS p1_nome,
                    p1.cognome AS p1_cognome,

                    p2.id AS p2_id,
                    p2.nome AS p2_nome,
                    p2.cognome AS p2_cognome

                    FROM Scuderie s
                    JOIN Piloti p1 ON s.pilota_attuale1_id = p1.id
                    JOIN Piloti p2 ON s.pilota_attuale2_id = p2.id
                    ORDER BY s.nome ASC ";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}
        if (!$stmt->execute()) {
            die("Errore durante l'esecuzione: " . $stmt->error);
        }

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function get_circuiti_page_data() {
        $query = "SELECT * FROM Circuiti ORDER BY nome ASC";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}
        if (!$stmt->execute()) {
            die("Errore durante l'esecuzione: " . $stmt->error);
        }

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function admin_delete_user($username) {
        $query_check = "SELECT adminPower FROM Utente WHERE username = ?";

        $stmt_check = $this->connection->prepare($query_check);
        if ($stmt_check === false) {
            die("Errore nella preparazione dello statement: " . $this->connection->error);
        }
        $stmt_check->bind_param("s", $username);
        if (!$stmt_check->execute()) {
            die("Errore durante l'esecuzione: " . $stmt_check->error);
        }
        $result_check = $stmt_check->get_result();
        $row = $result_check->fetch_assoc();
        if ($row['adminPower'] == 1) {
            return false;
        }

        $query = "DELETE FROM Utente WHERE username = ?";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

        $stmt->bind_param("s", $username);
        $result = $stmt->execute();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function admin_add_race($circuito_id, $data, $primo_posto, $secondo_posto, $terzo_posto){

		$query = "INSERT INTO Gare (circuito_id, data, primo_posto, secondo_posto, terzo_posto) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

        $stmt->bind_param("sssss", $circuito_id, $data, $primo_posto, $secondo_posto, $terzo_posto);
        $result = $stmt->execute();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function get_scuderie_page_data() {
        $query = "SELECT 
                    s.nome AS team_name,
                    p1.id AS p1_id,
                    p1.nome AS p1_nome,
                    p1.cognome AS p1_cognome,

                    p2.id AS p2_id,
                    p2.nome AS p2_nome,
                    p2.cognome AS p2_cognome

                    FROM Scuderie s
                    JOIN Piloti p1 ON s.pilota_attuale1_id = p1.id
                    JOIN Piloti p2 ON s.pilota_attuale2_id = p2.id
                    ORDER BY s.nome ASC ";   

        $stmt = $this->connection->prepare($query);

        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

        if (!$stmt->execute()) {
            die("Errore durante l'esecuzione: " . $stmt->error);
        }

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // --- FUNZIONI PER LE CLASSIFICHE ---

    // --- CLASSIFICA PILOTI 2025 ---
    public function get_drivers_standings() {
        // Uniamo la tabella ClassificaPiloti con Piloti (per i nomi) 
        // e Scuderie (per trovare il team attuale del pilota)
        $query = "SELECT 
                    cp.posizione, 
                    p.nome, 
                    p.cognome, 
                    s.nome AS nome_scuderia, 
                    cp.punti 
                  FROM ClassificaPiloti cp
                  JOIN Piloti p ON cp.pilota_id = p.id
                  LEFT JOIN Scuderie s ON (p.id = s.pilota_attuale1_id OR p.id = s.pilota_attuale2_id)
                  WHERE cp.anno = 2025
                  ORDER BY cp.posizione ASC";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}
        
        if (!$stmt->execute()) {
            die("Errore classifica piloti: " . $stmt->error);
        }

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // --- CLASSIFICA COSTRUTTORI 2025 ---
    public function get_constructors_standings() {
        // Query diretta sulla tabella ClassificaCostruttori
        $query = "SELECT 
                    posizione, 
                    scuderia_nome AS nome_scuderia, 
                    punti 
                  FROM ClassificaCostruttori 
                  WHERE anno = 2025
                  ORDER BY posizione ASC";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

        if (!$stmt->execute()) {
            die("Errore classifica costruttori: " . $stmt->error);
        }

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ---------------------------------------------
    public function get_gare_data() {
        $query = "SELECT 
                    G.id, 
                    G.data, 
                    C.nome AS circuito_nome, 
                    C.citta AS circuito_citta,
                    P1.nome AS p1_nome, P1.cognome AS p1_cognome, P1.id AS p1_id,
                    P2.nome AS p2_nome, P2.cognome AS p2_cognome, P2.id AS p2_id,
                    P3.nome AS p3_nome, P3.cognome AS p3_cognome, P3.id AS p3_id
                FROM Gare G
                JOIN Circuiti C ON G.circuito_id = C.id
                LEFT JOIN Piloti P1 ON G.primo_posto = P1.id
                LEFT JOIN Piloti P2 ON G.secondo_posto = P2.id
                LEFT JOIN Piloti P3 ON G.terzo_posto = P3.id
                ORDER BY G.data DESC";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

        if (!$stmt->execute()) {
            die("Errore nel recupero dati gare: " . $stmt->error);
        }

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // --- FUNZIONI ADMIN ---
    public function check_for_existing_race($id) {
        $query = "SELECT * FROM Gare WHERE id = ?";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

        $stmt->bind_param("s", $id);

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

    public function check_for_existing_pilot($id) {
        $query = "SELECT * FROM Piloti WHERE id = ?";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

        $stmt->bind_param("s", $id);

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

    public function admin_delete_race($id) {
        $query = "DELETE FROM Gare WHERE id = ?";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

        $stmt->bind_param("s", $id);
        $result = $stmt->execute();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function find_team_by_pilot_id($id_pilota) {
        $query = "SELECT nome FROM Scuderie WHERE pilota_attuale1_id = ? OR pilota_attuale2_id = ?";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

        $stmt->bind_param("ii", $id_pilota, $id_pilota);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row ? $row['nome'] : null;
    }

    public function admin_update_team_points($team_name, $punti){
        $query = "UPDATE ClassificaCostruttori SET punti = punti + ? WHERE scuderia_nome = ?";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

        $stmt->bind_param("is", $punti, $team_name);
        $result = $stmt->execute();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function admin_increase_pilot_points($id_pilota, $punti){
        $query = "UPDATE ClassificaPiloti SET punti = punti + ? WHERE pilota_id = ?";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

        $stmt->bind_param("ii", $punti, $id_pilota);
        $result = $stmt->execute();

        $current_team = $this->find_team_by_pilot_id($id_pilota);
        $this->admin_update_team_points($current_team, $punti);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function admin_decrease_pilot_points($id_pilota, $punti){
        $query = "UPDATE ClassificaPiloti SET punti = punti - ? WHERE pilota_id = ?";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
			die("Errore nella preparazione dello statement: " . $this->connection->error);
		}

        $stmt->bind_param("ii", $punti, $id_pilota);
        $result = $stmt->execute();
        
        $current_team = $this->find_team_by_pilot_id($id_pilota);
        $punti_meno = -$punti;
        $this->admin_update_team_points($current_team, $punti_meno);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function get_commenti($id_gara) {
        $query = "SELECT testo, data FROM Commento WHERE gara_id=? ORDER BY data DESC";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $id_gara);
        if (!$stmt->execute()) {
            die("Errore durante l'esecuzione: " . $stmt->error);
        }

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>