document.addEventListener('DOMContentLoaded', () => {
  const dropdowns = Array.from(document.querySelectorAll('[data-dropdown]'));

  const closeAll = (except) => {
    dropdowns.forEach((dropdown) => {
      if (dropdown === except) return;
      const button = dropdown.querySelector('[data-dropdown-toggle]');
      const menuId = button ? button.getAttribute('data-dropdown-toggle') : null;
      const menu = menuId ? document.getElementById(menuId) : null;
      if (button) button.setAttribute('aria-expanded', 'false');
      if (menu) menu.classList.remove('is-open');
    });
  };

  dropdowns.forEach((dropdown) => {
    const button = dropdown.querySelector('[data-dropdown-toggle]');
    if (!button) return;
    const menu = document.getElementById(button.getAttribute('data-dropdown-toggle'));
    if (!menu) return;

    button.addEventListener('click', (event) => {
      event.preventDefault();
      const isExpanded = button.getAttribute('aria-expanded') === 'true';
      closeAll(isExpanded ? null : dropdown);
      if (!isExpanded) {
        button.setAttribute('aria-expanded', 'true');
        menu.classList.add('is-open');
      }
    });

    button.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowDown') {
        event.preventDefault();
        button.setAttribute('aria-expanded', 'true');
        menu.classList.add('is-open');
        const firstItem = menu.querySelector('[role="menuitem"]');
        if (firstItem) firstItem.focus();
      }
    });

    menu.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        closeAll();
        button.focus();
      }
    });
  });

  document.addEventListener('click', (event) => {
    const container = event.target.closest('[data-dropdown]');
    if (!container) closeAll();
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeAll();
  });
});