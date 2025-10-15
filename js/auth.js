document.addEventListener('DOMContentLoaded', () => {
    const tabButtons = Array.from(document.querySelectorAll('[data-auth-tab]'));
    const panels = Array.from(document.querySelectorAll('[data-auth-panel]'));
    const switchLinks = Array.from(document.querySelectorAll('[data-switch-to]'));

    const setActivePanel = (name) => {
        if (!name) return;

        tabButtons.forEach((button) => {
            const isActive = button.dataset.authTab === name;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        panels.forEach((panel) => {
            const isActive = panel.dataset.authPanel === name;
            panel.classList.toggle('is-active', isActive);
            panel.setAttribute('aria-hidden', isActive ? 'false' : 'true');
        });

        const activePanel = panels.find((panel) => panel.dataset.authPanel === name);
        if (activePanel) {
            const focusTarget = activePanel.querySelector('input, select, button');
            if (focusTarget) {
                window.requestAnimationFrame(() => focusTarget.focus());
            }
        }
    };

    const initialName = tabButtons.find((button) => button.classList.contains('is-active'))?.dataset.authTab
        || panels.find((panel) => panel.classList.contains('is-active'))?.dataset.authPanel;

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setActivePanel(button.dataset.authTab);
        });
    });

    switchLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const target = link.dataset.switchTo;
            if (target) {
                setActivePanel(target);
            }
        });
    });

    if (initialName) {
        setActivePanel(initialName);
    }
});
