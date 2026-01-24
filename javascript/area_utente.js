const openBtn = document.getElementById('open-delete-modal');
const modal = document.getElementById('delete-modal');
const closeBtn = document.getElementById('close-modal');

openBtn.onclick = () => {
    modal.hidden = false;
    closeBtn.focus(); // Porta il focus sul tasto "Annulla" per sicurezza
};

closeBtn.onclick = () => {
    modal.hidden = true;
    openBtn.focus(); // Riporta il focus sul pulsante che ha aperto il modale
};

// Chiudi con il tasto ESC
window.onkeydown = (e) => {
    if (e.key === 'Escape' && !modal.hidden) {
        modal.hidden = true;
        openBtn.focus();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('open-delete-modal');
    const modal = document.getElementById('delete-modal');
    const closeBtn = document.getElementById('close-modal');

    if (openBtn && modal && closeBtn) {
        // Apri il modale
        openBtn.addEventListener('click', (e) => {
            e.preventDefault(); // Impedisce comportamenti strani
            modal.removeAttribute('hidden');
            closeBtn.focus();
        });

        // Chiudi il modale (Annulla)
        closeBtn.addEventListener('click', () => {
            modal.setAttribute('hidden', '');
            openBtn.focus();
        });

        // Chiudi se si clicca fuori dal contenuto (sull'overlay)
        modal.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) {
                modal.setAttribute('hidden', '');
                openBtn.focus();
            }
        });
    }
});