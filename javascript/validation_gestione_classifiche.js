document.addEventListener("DOMContentLoaded", function () {
    validate_gestione_classifiche();
});


function validate_gestione_classifiche() {
    var form = document.getElementById("form-modifica-punti-pilota");

    form.addEventListener("submit", function (event) {
        resetErrors(0);
        var err_messages = "";

        if (!validate_numero_punti()) {
            err_messages += "<p>I punti devono essere un numero intero positivo.</p>";
        }

        if (err_messages.trim().length !== 0) {
            setErrorMessage(err_messages, 0);
            event.preventDefault();
        }
    });
}

function validate_numero_punti() {
    var punti = document.getElementById("punti").value;
    var punti_regex = /^\d+$/;

    if (!punti_regex.test(punti) || parseInt(punti) < 0) {
        return false;
    }

    return true;
}