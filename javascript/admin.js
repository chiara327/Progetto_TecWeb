/*document.addEventListener("DOMContentLoaded", () => {

    const radios = document.querySelectorAll(
        '#gestione-sito-form input[type="radio"]'
    );

    const adminArea = document.getElementById("admin-area");

    radios.forEach(radio => {
        radio.addEventListener("change", () => {

            const area = radio.value;

            const radioAzioneSelezionato = document.querySelector('#gestione-azione-form input[type="radio"]:checked');
            const azione = radioAzioneSelezionato ? radioAzioneSelezionato.value : "";

            fetch("../php/get_admin_area.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `area=${encodeURIComponent(area)}&azione=${encodeURIComponent(azione)}`
                //body: "area=" + encodeURIComponent(area)
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

});*/
document.addEventListener("DOMContentLoaded", () => {
    
    // Selezioniamo tutti i radio button di entrambi i form
    const areaRadios = document.querySelectorAll('#gestione-sito-form input[type="radio"]');
    const azioneRadios = document.querySelectorAll('#gestione-azione-form input[type="radio"]');
    const adminArea = document.getElementById("admin-area");

    // Funzione principale per l'aggiornamento AJAX
    const updateAdminArea = () => {
        // Troviamo il radio selezionato per il gruppo "Area"
        const selectedArea = document.querySelector('#gestione-sito-form input[type="radio"]:checked');
        // Troviamo il radio selezionato per il gruppo "Azione"
        const selectedAction = document.querySelector('#gestione-azione-form input[type="radio"]:checked');

        // Se non è selezionato nulla in uno dei due, possiamo decidere di non inviare o inviare stringa vuota
        const areaValue = selectedArea ? selectedArea.value : "";
        const actionValue = selectedAction ? selectedAction.value : "";

        // Mostriamo un caricamento (opzionale ma consigliato)
        adminArea.innerHTML = "Caricamento...";

        fetch("../php/get_admin_area.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `area=${encodeURIComponent(areaValue)}&azione=${encodeURIComponent(actionValue)}`
        })
        .then(response => response.text())
        .then(html => {
            adminArea.innerHTML = html;
        })
        .catch(error => {
            adminArea.innerHTML = "<p class='error'>Errore nel caricamento.</p>";
            console.error(error);
        });
    };

    // Colleghiamo la funzione a tutti i radio del primo gruppo
    areaRadios.forEach(radio => {
        radio.addEventListener("change", updateAdminArea);
    });

    // Colleghiamo la funzione a tutti i radio del secondo gruppo
    azioneRadios.forEach(radio => {
        radio.addEventListener("change", updateAdminArea);
    });

});
