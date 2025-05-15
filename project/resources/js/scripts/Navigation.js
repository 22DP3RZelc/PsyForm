export default function Navigation() {
    document.addEventListener('click', (event) => {
        const button = event.target.closest('.dropdown-btn');
        const dropdownKey = button?.getAttribute('data-dropdown-key');
        const dropdownMenu = dropdownKey
            ? document.querySelector(`.dropdown-menu[data-dropdown-key="${dropdownKey}"]`)
            : null;

        document.querySelectorAll('.dropdown-menu').forEach((menu) => {
            if (menu !== dropdownMenu) {
                menu.classList.add('hidden');
            }
        });

        if (dropdownMenu) {
            dropdownMenu.classList.toggle('hidden');
        }
    });
}