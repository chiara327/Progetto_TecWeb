document.addEventListener("DOMContentLoaded", () => {

    const radios = document.querySelectorAll(
        '#gestione-sito-form input[type="radio"]'
    );

    const adminArea = document.getElementById("admin-area");

    radios.forEach(radio => {
        radio.addEventListener("change", () => {

            const area = radio.value;

            fetch("../php/get_admin_area.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "area=" + encodeURIComponent(area)
            })
            .then(response => response.text())
            .then(html => {
                adminArea.innerHTML = html;
            })
            .catch(error => {
                adminArea.innerHTML =
                    "<p class='error'>Errore nel caricamento.</p>";
                console.error(error);
            });

        });
    });

});
