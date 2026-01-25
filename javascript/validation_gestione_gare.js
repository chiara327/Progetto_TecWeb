document.addEventListener("DOMContentLoaded", function () {
    validate_gestione_gare();
});

function validate_gestione_gare() {
    var form = document.getElementById("form-gare-aggiungi");

    form.addEventListener("submit", function (event) {
        resetErrors(0);
        var err_messages = "";

        if (!validate_data_gara()) {
            err_messages += "<p>La data della gara non è valida, scegliere una data nel formato yyyy-mm-dd, che non sia nel futuro e che appartenga agli anni 2025 o 2026.</p>";
        }

        if (!validate_piloti_selezionati()) {
            err_messages += "<p>I piloti selezionati per i primi tre posti devono essere differenti tra loro.</p>";
        }

        if (err_messages.trim().length !== 0) {
            setErrorMessage(err_messages, 0);
            event.preventDefault();
        }
    });
}

function validate_data_gara() {
    var data_gara = document.getElementById("create_data").value;

    var today = new Date();
    today.setHours(0, 0, 0, 0);

    var inputDate = new Date(data_gara);
    inputDate.setHours(0, 0, 0, 0);

    var year = inputDate.getFullYear();

    if (isNaN(inputDate.getTime()) || inputDate > today || (year !== 2025 && year !== 2026)) {
        return false;
    }

    return true;
}

function validate_piloti_selezionati() {
    var pilota_primo_posto = document.getElementById("create_primo_posto").value;
    var pilota_secondo_posto = document.getElementById("create_secondo_posto").value;
    var pilota_terzo_posto = document.getElementById("create_terzo_posto").value;

    if (pilota_primo_posto === pilota_secondo_posto || pilota_primo_posto === pilota_terzo_posto || pilota_secondo_posto === pilota_terzo_posto) {
        return false;
    }

    return true;
}