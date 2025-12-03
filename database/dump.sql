PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE Scuderie (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL UNIQUE,
    presenze INTEGER, -- (?), numero di partecipazioni
    pilota_attuale1_id INTEGER, -- riferimento a Piloti.id (vedi nota)
    pilota_attuale2_id INTEGER, -- riferimento a Piloti.id (vedi nota)
    punti_campionato INTEGER DEFAULT 0,
    titoli INTEGER DEFAULT 0
    -- Nota: non imponiamo FORK diretta qui verso Piloti per evitare riferimenti circolari
);
CREATE TABLE Piloti (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    cognome TEXT NOT NULL,
    team_id INTEGER, -- foreign key verso Scuderie.id (Nome Team Attuale)
    numero INTEGER,
    vittorie INTEGER DEFAULT 0,
    n_pole INTEGER DEFAULT 0,
    gran_premi INTEGER DEFAULT 0,
    titoli_mondiali INTEGER DEFAULT 0,
    punti INTEGER DEFAULT 0,
    eta INTEGER,
    CONSTRAINT fk_team FOREIGN KEY (team_id) REFERENCES Scuderie(id) ON DELETE SET NULL ON UPDATE CASCADE
);
INSERT INTO Piloti VALUES(1,'Max','Verstappen',1,1,54,32,203,3,575,26);
CREATE TABLE Circuiti (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL UNIQUE,
    citta TEXT,
    nazione TEXT,
    lunghezza REAL, -- in km (o unit?? decisa)
    numero_curve INTEGER
);
CREATE TABLE ClassificaPiloti (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    anno INTEGER NOT NULL,
    pilota_id INTEGER NOT NULL, -- riferimento a Piloti.id
    posizione INTEGER,
    punti INTEGER DEFAULT 0,
    CONSTRAINT fk_pilota_stand FOREIGN KEY (pilota_id) REFERENCES Piloti(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT idx_anno_pilota UNIQUE (anno, pilota_id)
);
CREATE TABLE ClassificaCostruttori (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    anno INTEGER NOT NULL,
    scuderia_id INTEGER NOT NULL, -- riferimento a Scuderie.id
    posizione INTEGER,
    punti INTEGER DEFAULT 0,
    CONSTRAINT fk_scuderia_stand FOREIGN KEY (scuderia_id) REFERENCES Scuderie(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT idx_anno_scuderia UNIQUE (anno, scuderia_id)
);
CREATE TABLE Gare (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome_gara TEXT NOT NULL,
    citta_gara TEXT,
    data DATE
);
CREATE TABLE RisultatiGara (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    gara_id INTEGER NOT NULL,
    pilota_id INTEGER NOT NULL,
    posizione INTEGER,
    CONSTRAINT fk_gara FOREIGN KEY (gara_id) REFERENCES Gare(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_pilota FOREIGN KEY (pilota_id) REFERENCES Piloti(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT idx_gara_pilota UNIQUE (gara_id, pilota_id)
);
PRAGMA writable_schema=ON;
CREATE TABLE IF NOT EXISTS sqlite_sequence(name,seq);
DELETE FROM sqlite_sequence;
INSERT INTO sqlite_sequence VALUES('Piloti',1);
CREATE INDEX idx_piloti_team ON Piloti(team_id);
CREATE INDEX idx_risultati_gara ON RisultatiGara(gara_id);
CREATE INDEX idx_risultati_pilota ON RisultatiGara(pilota_id);
PRAGMA writable_schema=OFF;
COMMIT;
