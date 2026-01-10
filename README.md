# Progetto_TecWeb

## 🚀 Come utilizzare docker-compose

### Installazione di Docker

Installa docker:
  - dal sito ufficiale
  - tramite riga di comando (linux enjoyer only)

### Gestione del Server (Comandi Terminale)

Usa questi comandi all'interno della cartella Progetto_TecWeb:

  - Avvio iniziale (o dopo modifiche al Dockerfile): `docker-compose up -d --build`
  - Avvio rapido: `docker-compose up -d`
  - Vedere se i container sono attivi: `docker-compose ps`
  - Spegnere tutto: `docker-compose stop`
  - Reset totale (cancella anche i dati del DB): `docker-compose down -v`

### Parametri di Connessione Database (PHP)

Modifica il tuo file db_connection.php con questi parametri per Docker:
```
private const HOST = "db"; 
private const DB_NAME = "f1_db"; 
private const USER = "root"; 
private const PASSWORD = "root_password"; 
```

### Indirizzi di Accesso

Sito Web: http://localhost:8080

phpMyAdmin: http://localhost:8081 
  - User: root
  - Pass: root_password
