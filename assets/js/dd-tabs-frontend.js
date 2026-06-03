/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 */
document.addEventListener('DOMContentLoaded', function () {

    function initDDTabs() {
        const tabWrappers = document.querySelectorAll('.dd-tabs-wrapper');

        tabWrappers.forEach(function (wrapper) {
            const panels = wrapper.querySelectorAll('.dd-tab-panel');
            if (panels.length === 0) return;

            const desktopNav = document.createElement('div');
            desktopNav.className = 'dd-tabs-nav-desktop';
            wrapper.insertBefore(desktopNav, wrapper.firstChild);

            const desktopButtons = [];
            const accordionButtons = [];

            const activateTab = function (targetIndex) {
                panels.forEach((p, i) => {
                    if (i === targetIndex) {
                        p.classList.add('active');
                        p.style.removeProperty('display'); // Let Gutenberg classes dictate flex/block layouts
                        desktopButtons[i].classList.add('active');
                        desktopButtons[i].setAttribute('aria-selected', 'true');
                        accordionButtons[i].classList.add('active');
                        accordionButtons[i].setAttribute('aria-expanded', 'true');
                    } else {
                        p.classList.remove('active');
                        p.style.display = 'none'; // Force hide inactive panels
                        desktopButtons[i].classList.remove('active');
                        desktopButtons[i].setAttribute('aria-selected', 'false');
                        accordionButtons[i].classList.remove('active');
                        accordionButtons[i].setAttribute('aria-expanded', 'false');
                    }
                });
            };

            panels.forEach(function (panel, index) {
                const title = panel.getAttribute('data-tab-title') || 'Tab';

                const dBtn = document.createElement('button');
                dBtn.className = 'dd-tab-button';
                dBtn.innerText = title;
                dBtn.setAttribute('role', 'tab');
                desktopNav.appendChild(dBtn);
                desktopButtons.push(dBtn);

                const aBtn = document.createElement('button');
                aBtn.className = 'dd-accordion-button';
                aBtn.innerHTML = `<span>${title}</span><span class="dd-accordion-icon"></span>`;
                panel.parentNode.insertBefore(aBtn, panel);
                accordionButtons.push(aBtn);

                dBtn.addEventListener('click', function () { activateTab(index); });
                aBtn.addEventListener('click', function () {
                    if (aBtn.classList.contains('active')) {
                        panel.classList.remove('active');
                        panel.style.display = 'none';
                        aBtn.classList.remove('active');
                        aBtn.setAttribute('aria-expanded', 'false');
                        dBtn.classList.remove('active');
                        dBtn.setAttribute('aria-selected', 'false');
                    } else {
                        activateTab(index);
                    }
                });
            });

            activateTab(0);
        });
    }

    initDDTabs();
});