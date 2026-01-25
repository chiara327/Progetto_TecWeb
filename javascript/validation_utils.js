function setErrorMessage(err, form) {
    var errorDiv = document.getElementsByClassName("form_errors")[form];
    errorDiv.innerHTML += err;
}

function resetErrors(form) {
    var errorDiv = document.getElementsByClassName("form_errors")[form];
    errorDiv.innerHTML = "";
}