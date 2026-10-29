'use strict';

function clearSearch() {
    const input = document.querySelector('.searchInput');
    if (input) {
        input.value = '';
        input.dispatchEvent(new Event('input'));
    }
}

function validatePasswordMatch() {
    const mdp1 = document.getElementById('mdp1');
    const mdp2 = document.getElementById('mdp2');
    if (mdp1 && mdp2) {
        if (mdp1.value !== mdp2.value) {
            mdp2.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            mdp2.setCustomValidity('');
        }
    }
}

function myFunction() {
    const nav = document.getElementById("myMenunav");
    if (nav) {
        nav.classList.toggle("break");
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // 1. Gestion du thème Clair / Sombre avec persistance localStorage
    const themeBody = document.getElementById("themeBody") || document.body;
    const boutonTheme = document.getElementById("boutonTheme");
    const iconeTheme = document.querySelector('.iconeTheme');

    const savedTheme = localStorage.getItem('themeClass') || 'clair-theme';
    const savedIcon = localStorage.getItem('themeIcon') || 'dark_mode';

    themeBody.className = savedTheme;
    if (boutonTheme) boutonTheme.textContent = savedIcon;
    if (iconeTheme) iconeTheme.textContent = savedIcon;

    function handleThemeChange(e) {
        if (e) e.preventDefault();
        const isClair = themeBody.classList.contains('clair-theme');
        const nextTheme = isClair ? 'sombre-theme' : 'clair-theme';
        const nextIcon = isClair ? 'light_mode' : 'dark_mode';

        themeBody.className = nextTheme;
        localStorage.setItem('themeClass', nextTheme);
        localStorage.setItem('themeIcon', nextIcon);

        if (boutonTheme) boutonTheme.textContent = nextIcon;
        if (iconeTheme) iconeTheme.textContent = nextIcon;
    }

    if (boutonTheme) boutonTheme.addEventListener('click', handleThemeChange);
    if (iconeTheme) {
        const parentLink = iconeTheme.closest('a') || iconeTheme;
        parentLink.addEventListener('click', handleThemeChange);
    }

    // 2. Mise en évidence automatique de l'onglet actif dans la navbar
    const navLinks = document.querySelectorAll('#myMenunav a[href^="?url="]');
    const params = new URLSearchParams(window.location.search);
    const currentUrl = (params.get('url') || 'mediabox/index').toLowerCase();
    const currentController = currentUrl.split('/')[0];

    navLinks.forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href').toLowerCase();
        if (href.includes(`?url=${currentController}/`)) {
            link.classList.add('active');
        }
    });

    // 3. Accordéons éventuels (.collapsible)
    const collapsibles = document.querySelectorAll(".collapsible");
    collapsibles.forEach(element => {
        element.addEventListener("click", function () {
            this.classList.toggle("activeCollapse");
            const content = this.nextElementSibling;
            if (content) {
                if (content.style.maxHeight) {
                    content.style.maxHeight = null;
                } else {
                    content.style.maxHeight = (content.scrollHeight + 20) + "px";
                }
            }
        });
    });
});