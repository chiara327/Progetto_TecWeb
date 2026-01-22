SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES 'utf8mb4';
SET CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS Piloti (
    id INT NOT NULL,
    nome VARCHAR(50) NOT NULL,
    cognome VARCHAR(50) NOT NULL,
    numero INT DEFAULT NULL,
    vittorie INT DEFAULT 0,
    n_pole INT DEFAULT 0,
    gran_premi INT DEFAULT 0,
    titoli_mondiali INT DEFAULT 0,
    punti INT DEFAULT 0,
    eta INT DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO Piloti (id, nome, cognome, numero, vittorie, n_pole, gran_premi, titoli_mondiali, punti, eta) VALUES
(1, 'Max', 'Verstappen', 1, 58, 36, 215, 3, 620, 27),
(2, 'Yuki', 'Tsunoda', 22, 1, 0, 85, 0, 145, 24),
(3, 'Lewis', 'Hamilton', 44, 105, 69, 310, 7, 1300, 40),
(4, 'Charles', 'Leclerc', 16, 9, 26, 150, 0, 980, 27),
(5, 'Lando', 'Norris', 4, 6, 7, 140, 0, 510, 25),
(6, 'Oscar', 'Piastri', 81, 4, 3, 90, 0, 320, 24),
(7, 'Fernando', 'Alonso', 14, 32, 22, 385, 2, 2200, 43),
(8, 'Lance', 'Stroll', 18, 1, 1, 150, 0, 310, 27),
(9, 'George', 'Russell', 63, 5, 4, 120, 0, 410, 27),
(10, 'Andrea Kimi', 'Antonelli', 12, 0, 0, 22, 0, 18, 18),
(11, 'Carlos', 'Sainz', 55, 6, 4, 190, 0, 720, 30),
(12, 'Alex', 'Albon', 23, 2, 2, 115, 0, 280, 29),
(13, 'Esteban', 'Ocon', 31, 2, 0, 145, 0, 360, 28),
(14, 'Oliver', 'Bearman', 10, 3, 2, 155, 0, 395, 29),
(15, 'Liam', 'Lawson', 77, 10, 20, 240, 0, 1800, 35),
(16, 'Isack', 'Hadjar', 24, 0, 0, 65, 0, 38, 26),
(17, 'Pierre', 'Gasly', 10, 3, 2, 155, 0, 395, 29),
(18, 'Franco', 'Colapinto', 3, 8, 3, 240, 0, 1320, 35),
(19, 'Nico', 'Hulkenberg', 27, 0, 1, 210, 0, 530, 37),
(20, 'Gabriel', 'Bortoleto', 88, 0, 1, 210, 0, 530, 37);

CREATE TABLE IF NOT EXISTS Scuderie (
    nome VARCHAR(100) NOT NULL,
    presenze INT DEFAULT 0,
    pilota_attuale1_id INT DEFAULT NULL,
    pilota_attuale2_id INT DEFAULT NULL,
    punti_campionato INT DEFAULT 0,
    titoli INT DEFAULT 0,
    PRIMARY KEY (nome)
) ENGINE=InnoDB;

INSERT INTO Scuderie (nome, presenze, pilota_attuale1_id, pilota_attuale2_id, punti_campionato, titoli) VALUES
('McLaren', 950, 5, 6, 640, 8),
('Mercedes', 520, 9, 10, 680, 8),
('Red Bull Racing', 380, 1, 2, 860, 6),
('Ferrari', 1050, 3, 4, 720, 16),
('Williams', 800, 11, 12, 190, 9),
('RB', 90, 15, 16, 260, 0),
('Aston Martin', 150, 7, 8, 420, 0),
('Haas', 180, 13, 14, 140, 0),
('Sauber', 520, 19, 20, 120, 0),
('Alpine', 420, 17, 18, 310, 2);

CREATE TABLE IF NOT EXISTS Circuiti (
    id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    citta VARCHAR(100),
    nazione VARCHAR(100),
    lunghezza DECIMAL(5,3), 
    numero_curve INT,
    PRIMARY KEY (id),
    UNIQUE INDEX idx_nome_circuito (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO Circuiti (id, nome, citta, nazione, lunghezza, numero_curve) VALUES
(1, 'Albert Park Circuit', 'Melbourne', 'Australia', 5.278, 14),
(2, 'Shanghai International Circuit', 'Shanghai', 'Cina', 5.451, 16),
(3, 'Suzuka International Racing Course', 'Suzuka', 'Giappone', 5.807, 18),
(4, 'Bahrain International Circuit', 'Sakhir', 'Bahrain', 5.412, 15),
(5, 'Jeddah Corniche Circuit', 'Jeddah', 'Arabia Saudita', 6.174, 27),
(6, 'Miami International Autodrome', 'Miami', 'Stati Uniti', 5.412, 19),
(7, 'Autodromo Enzo e Dino Ferrari', 'Imola', 'Italia', 4.909, 19),
(8, 'Circuit de Monaco', 'Monte Carlo', 'Monaco', 3.337, 19),
(9, 'Circuit de Barcelona-Catalunya', 'Montmeló', 'Spagna', 4.657, 14),
(10, 'Circuit Gilles-Villeneuve', 'Montreal', 'Canada', 4.361, 14),
(11, 'Red Bull Ring', 'Spielberg', 'Austria', 4.318, 10),
(12, 'Silverstone Circuit', 'Silverstone', 'Regno Unito', 5.891, 18),
(13, 'Circuit de Spa-Francorchamps', 'Stavelot', 'Belgio', 7.004, 19),
(14, 'Hungaroring', 'Mogyoród', 'Ungheria', 4.381, 14),
(15, 'Circuit Zandvoort', 'Zandvoort', 'Paesi Bassi', 4.259, 14),
(16, 'Autodromo Nazionale di Monza', 'Monza', 'Italia', 5.793, 11),
(17, 'Baku City Circuit', 'Baku', 'Azerbaigian', 6.003, 20),
(18, 'Marina Bay Street Circuit', 'Singapore', 'Singapore', 4.940, 19),
(19, 'Circuit of the Americas', 'Austin', 'Stati Uniti', 5.513, 20),
(20, 'Autódromo Hermanos Rodríguez', 'Città del Messico', 'Messico', 4.304, 17),
(21, 'Autódromo José Carlos Pace', 'San Paolo', 'Brasile', 4.309, 15),
(22, 'Las Vegas Strip Circuit', 'Las Vegas', 'Stati Uniti', 6.201, 17),
(23, 'Lusail International Circuit', 'Lusail', 'Qatar', 5.419, 16),
(24, 'Yas Marina Circuit', 'Abu Dhabi', 'Emirati Arabi Uniti', 5.281, 16),
(25, 'Circuito Piovego', 'Padova', 'Italia', 3.128, 13);

CREATE TABLE IF NOT EXISTS ClassificaPiloti (
    id INT NOT NULL,
    anno INT NOT NULL,
    pilota_id INT NOT NULL,
    posizione INT,
    punti INT DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE INDEX idx_anno_pilota (anno, pilota_id),
    CONSTRAINT fk_pilota_stand FOREIGN KEY (pilota_id) 
        REFERENCES Piloti(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO ClassificaPiloti
(id, anno, pilota_id, posizione, punti)
VALUES
(1, 2025, 1, 1, 423),   
(2, 2025, 4, 2, 421),   
(3, 2025, 3, 3, 410),  
(4, 2025, 5, 4, 319),  
(5, 2025, 11, 5, 242),  
(6, 2025, 9, 6, 156),    
(7, 2025, 7, 7, 150),   
(8, 2025, 6, 8, 125),   
(9, 2025, 2, 9, 83),    
(10, 2025, 14, 10, 67), 
(11, 2025, 13, 11, 42), 
(12, 2025, 17, 12, 36), 
(13, 2025, 18, 13, 31), 
(14, 2025, 12, 14, 29), 
(15, 2025, 19, 15, 18), 
(16, 2025, 20, 16, 16), 
(17, 2025, 15, 17, 12), 
(18, 2025, 16, 18, 7), 
(19, 2025, 8, 19, 2),  
(20, 2025, 10, 20, 1); 


CREATE TABLE IF NOT EXISTS ClassificaCostruttori (
    anno INT NOT NULL,
    scuderia_nome VARCHAR(100) NOT NULL,
    posizione INT,
    punti INT DEFAULT 0,
    PRIMARY KEY (anno, scuderia_nome),
    CONSTRAINT fk_scuderia_stand FOREIGN KEY (scuderia_nome) 
        REFERENCES Scuderie(nome) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO ClassificaCostruttori
(anno, scuderia_nome, posizione, punti)
VALUES
(2025, 'Red Bull Racing', 1, 860),
(2025, 'Ferrari', 2, 720),
(2025, 'Mercedes', 3, 680),
(2025, 'McLaren', 4, 640),
(2025, 'Aston Martin', 5, 420),
(2025, 'Alpine', 6, 310),
(2025, 'RB', 7, 260),
(2025, 'Williams', 8, 190),
(2025, 'Haas', 9, 140),
(2025, 'Sauber', 10, 120);

CREATE TABLE IF NOT EXISTS Gare (
    id INT NOT NULL,
    circuito_id INT NOT NULL,
    data DATE,
    primo_posto INT,
    secondo_posto INT,
    terzo_posto INT,
    PRIMARY KEY (id),
    CONSTRAINT fk_circuito_gara FOREIGN KEY (circuito_id) 
        REFERENCES Circuiti(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_primo_posto FOREIGN KEY (primo_posto)
        REFERENCES Piloti(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_secondo_posto FOREIGN KEY (secondo_posto)
        REFERENCES Piloti(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_terzo_posto FOREIGN KEY (terzo_posto)
        REFERENCES Piloti(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Svuota la tabella se vuoi evitare duplicati di ID prima di inserire
-- DELETE FROM Gare; 

INSERT INTO Gare (id, circuito_id, data, primo_posto, secondo_posto, terzo_posto) VALUES
(1, 1, '2025-03-16', 5, 4, 1),    -- Australia: Norris, Leclerc, Verstappen
(2, 2, '2025-03-23', 1, 5, 3),    -- Cina: Verstappen, Norris, Hamilton
(3, 3, '2025-04-06', 1, 4, 2),    -- Giappone: Verstappen, Leclerc, Tsunoda
(4, 4, '2025-04-13', 4, 1, 11),   -- Bahrain: Leclerc, Verstappen, Sainz
(5, 5, '2025-04-20', 1, 9, 4),    -- Arabia Saudita: Verstappen, Russell, Leclerc
(6, 6, '2025-05-04', 5, 1, 6),    -- Miami: Norris, Verstappen, Piastri
(7, 7, '2025-05-18', 4, 11, 1),   -- Imola: Leclerc, Sainz, Verstappen
(8, 8, '2025-05-25', 4, 6, 11),   -- Monaco: Leclerc, Piastri, Sainz
(9, 9, '2025-06-01', 1, 5, 3),    -- Spagna: Verstappen, Norris, Hamilton
(10, 10, '2025-06-15', 3, 1, 9),  -- Canada: Hamilton, Verstappen, Russell
(11, 11, '2025-06-29', 9, 6, 11), -- Austria: Russell, Piastri, Sainz
(12, 12, '2025-07-06', 3, 5, 1),  -- Regno Unito: Hamilton, Norris, Verstappen
(13, 13, '2025-07-27', 1, 3, 4),  -- Belgio: Verstappen, Hamilton, Leclerc
(14, 14, '2025-08-03', 6, 5, 3),  -- Ungheria: Piastri, Norris, Hamilton
(15, 15, '2025-08-31', 5, 1, 4),  -- Paesi Bassi: Norris, Verstappen, Leclerc
(16, 16, '2025-09-07', 11, 4, 1), -- Italia: Sainz, Leclerc, Verstappen (Festa Ferrari!)
(17, 17, '2025-09-21', 6, 4, 9),  -- Azerbaigian: Piastri, Leclerc, Russell
(18, 18, '2025-10-05', 5, 1, 6),  -- Singapore: Norris, Verstappen, Piastri
(19, 19, '2025-10-19', 4, 5, 1),  -- Stati Uniti (Austin): Leclerc, Norris, Verstappen
(20, 20, '2025-10-26', 11, 5, 4), -- Messico: Sainz, Norris, Leclerc
(21, 21, '2025-11-09', 1, 17, 3), -- Brasile: Verstappen, Gasly, Hamilton
(22, 22, '2025-11-22', 9, 3, 1),  -- Las Vegas: Russell, Hamilton, Verstappen
(23, 23, '2025-11-30', 1, 5, 6),  -- Qatar: Verstappen, Norris, Piastri
(24, 24, '2025-12-07', 1, 4, 5),  -- Abu Dhabi: Verstappen, Leclerc, Norris
(25, 25, '2025-12-14', 1, 2, 3);  -- Padova: Verstappen, Tsunoda, Hamilton

CREATE TABLE IF NOT EXISTS Utente (
    username VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    adminPower TINYINT(1) NOT NULL DEFAULT 0,
    nome VARCHAR(30) NOT NULL,
    cognome VARCHAR(30) NOT NULL,
    dataNascita DATE NOT NULL,
    PRIMARY KEY (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO Utente (username, password, adminPower, nome, cognome, dataNascita) VALUES
('luigi', '0000', 1, 'Luigi', 'Verdi', '2025-01-01'),
('Admin', '$2y$10$3nBJqkuPFy9aHA8pMCOyZegRUQbGCvGN56aFPdX/qhgW6Ju6b45j2', 1, 'Mario', 'Rossi', '2000-01-01'),
('pippo99', '1234', 0, 'Filippo', 'Pippino', '2025-01-01');


CREATE TABLE IF NOT EXISTS Commento (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(30) NOT NULL,
    gara_id INT NOT NULL,
    testo TEXT NOT NULL,
    data DATE NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_utente_commento FOREIGN KEY (username) 
        REFERENCES Utente(username) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_gara_commento FOREIGN KEY (gara_id) 
        REFERENCES Gare(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO Commento (username, gara_id, testo, data) VALUES
('luigi', 1, 'Grande gara di Verstappen, ha dominato dall''inizio alla fine!', '2025-09-08'),
('pippo99', 1, 'Leclerc ha fatto un ottimo lavoro per la Ferrari, ma non è bastato.', '2025-09-08');