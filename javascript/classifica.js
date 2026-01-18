document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('[role="tab"]');
    const tabList = document.querySelector('[role="tablist"]');

    tabs.forEach(tab => {
        // Gestione del CLICK
        tab.addEventListener('click', e => {
            changeTabs(e.target);
        });

        // Gestione della TASTIERA (Frecce)
        tab.addEventListener('keydown', e => {
            let tabFocus = 0;
            // Trova l'indice del tab corrente
            for (let i = 0; i < tabs.length; i++) {
                if (tabs[i] === e.target) tabFocus = i;
            }

            if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                tabs[tabFocus].setAttribute("tabindex", -1);
                if (e.key === 'ArrowRight') {
                    tabFocus++;
                    if (tabFocus >= tabs.length) tabFocus = 0;
                } else if (e.key === 'ArrowLeft') {
                    tabFocus--;
                    if (tabFocus < 0) tabFocus = tabs.length - 1;
                }
                
                tabs[tabFocus].setAttribute("tabindex", 0);
                tabs[tabFocus].focus();
                // Opzionale: cambia tab automaticamente al focus
                // changeTabs(tabs[tabFocus]); 
            }
        });
    });
});

function changeTabs(target) {
    const parent = target.parentNode;
    const grandparent = parent.parentNode;

    // Rimuovi stato attivo da tutti i tab
    parent
        .querySelectorAll('[aria-selected="true"]')
        .forEach(t => {
            t.setAttribute("aria-selected", false);
            t.setAttribute("tabindex", -1);
        });

    // Imposta attivo il tab cliccato
    target.setAttribute("aria-selected", true);
    target.setAttribute("tabindex", 0);

    // Nascondi tutti i pannelli
    grandparent
        .querySelectorAll('[role="tabpanel"]')
        .forEach(p => p.setAttribute("hidden", true));

    // Mostra il pannello corrispondente
    grandparent.parentNode
        .querySelector(`#${target.getAttribute("aria-controls")}`)
        .removeAttribute("hidden");
}