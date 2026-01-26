document.addEventListener("DOMContentLoaded", function () {
    validate_gestione_utenti();
});

function validate_gestione_utenti() {
    var form = document.getElementById("form-utenti-aggiungi");

    form.addEventListener("submit", function (event) {
        resetErrors(0);
        var err_messages = "";

        if (!validateNome()) {
            err_messages += "<p>Il nome deve contenere solo lettere, spazi, trattini e apostrofi.</p>";
        }

        if (!validateCognome()) {
            err_messages += "<p>Il cognome deve contenere solo lettere, spazi, trattini e apostrofi.</p>";
        }

        if (!validateDate()) {
            err_messages += "<p>La data inserita non è valida, inserisci una data che non sia nel futuro.</p>";
        }

        if (!validateUsername()) {
            err_messages += "<p>Lo <span lang='en'>username</span> non deve superare i 30 caratteri.</p>";
        }

        if (!validatePassword()) {
            err_messages += "<p>La <span lang='en'>password</span> deve essere lunga almeno 8 caratteri e contenere: 1 lettera minuscola, 1 lettera maiuscola, 1 numero e 1 carattere speciale.</p>";
        }

        if (err_messages.trim().length !== 0) {
            setErrorMessage(err_messages, 0);
            event.preventDefault();
        }
    });
}

function validateNome() {
    var nome = document.getElementById("create_nome").value;
    var name_regex = /^[A-Za-zÀ-ÿ\s\-\']+$/;

    if (!name_regex.test(nome)) {
        return false;
    }

    return true;
}

function validateCognome() {
    var cognome = document.getElementById("create_cognome").value;
    var name_regex = /^[A-Za-zÀ-ÿ\s\-\']+$/;

    if (!name_regex.test(cognome)) {
        return false;
    }

    return true;
}

function validateUsername() {
    var username = document.getElementById("create_username").value;

    if (username.length > 30) {
        return false;
    }

    return true;
}

function validatePassword() {
    var password = document.getElementById("create_password").value;
    var password_regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (!password_regex.test(password)) {
        return false;
    }

    return true;
}

function validateDate() {
    var date = document.getElementById("create_data").value;
    var date_regex = /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/;

    if (!date_regex.test(date)) {
        return false;
    }

    var dateObj = new Date(date);
    if (dateObj > new Date()) {
        return false;
    }

    return true;
}