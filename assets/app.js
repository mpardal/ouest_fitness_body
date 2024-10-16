//import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

document.addEventListener('DOMContentLoaded', () => {
    const menuItems = document.querySelectorAll('.group > a');

    menuItems.forEach(item => {
        item.addEventListener('click', (event) => {
            event.preventDefault();
            const submenu = item.nextElementSibling;
            submenu.classList.toggle('hidden');
        });
    });
});

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
