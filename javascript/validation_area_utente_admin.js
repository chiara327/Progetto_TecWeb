document.addEventListener("DOMContentLoaded", function () {
    validate_gestione_informazioni_personali();
    validate_gestione_username();
    validate_gestione_password();
});

function validate_gestione_informazioni_personali() {
    var form = document.getElementById("modifica-anagrafica");

    form.addEventListener("submit", function (event) {
        resetErrors(0);
        var err_messages = "";

        if (!validateNome("modifica_nome")) {
            err_messages += "<p>Il nome deve contenere solo lettere, spazi, trattini e apostrofi.</p>";
        }

        if (!validateCognome("modifica_cognome")) {
            err_messages += "<p>Il cognome deve contenere solo lettere, spazi, trattini e apostrofi.</p>";
        }

        if (!validateDate("modifica_data")) {
            err_messages += "<p>La data inserita non è valida, inserisci una data che non sia nel futuro.</p>";
        }

        if (err_messages.trim().length !== 0) {
            setErrorMessage(err_messages, 0);
            event.preventDefault();
        }
    });
}

function validate_gestione_username() {
    var form = document.getElementById("modifica-username");

    form.addEventListener("submit", function (event) {
        resetErrors(1);
        var err_messages = "";

        if (!validateUsername("nuovo_username")) {
            err_messages += "<p>Lo <span lang='en'>username</span> non deve superare i 30 caratteri.</p>";
        }

        if (err_messages.trim().length !== 0) {
            setErrorMessage(err_messages, 1);
            event.preventDefault();
        }
    });
}

function validate_gestione_password() {
    var form = document.getElementById("modifica-password");

    form.addEventListener("submit", function (event) {
        resetErrors(2);
        var err_messages = "";

        if (!validatePassword("nuova_password")) {
            err_messages += "<p>La <span lang='en'>password</span> deve essere lunga almeno 8 caratteri e contenere: 1 lettera minuscola, 1 lettera maiuscola, 1 numero e 1 carattere speciale.</p>";
        }

        if (err_messages.trim().length !== 0) {
            setErrorMessage(err_messages, 2);
            event.preventDefault();
        }
    });
}

function validateNome(id) {
    var nome = document.getElementById(id).value;
    var name_regex = /^[A-Za-zÀ-ÿ\s\-\']+$/;

    if (!name_regex.test(nome)) {
        return false;
    }

    return true;
}

function validateCognome(id) {
    var cognome = document.getElementById(id).value;
    var name_regex = /^[A-Za-zÀ-ÿ\s\-\']+$/;

    if (!name_regex.test(cognome)) {
        return false;
    }

    return true;
}

function validateDate(id) {
    var date = document.getElementById(id).value;
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

function validateUsername(id) {
    var username = document.getElementById(id).value;

    if (username.length > 30) {
        return false;
    }

    return true;
}

function validatePassword(id) {
    var password = document.getElementById(id).value;
    var password_regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (!password_regex.test(password)) {
        return false;
    }

    return true;
}

