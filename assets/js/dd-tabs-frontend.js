/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Handles frontend interactivity, including Desktop Tabs and Mobile Accordion conversions.
 */
document.addEventListener('DOMContentLoaded', function () {
    
    /**
     * Initializes all Tab blocks on the DOM.
     * * Evaluates the mobile accordion dataset and builds responsive navigation elements.
     * * @return {void}
     */
    function initDDTabs() {
        const tabWrappers = document.querySelectorAll('.dd-tabs-wrapper');
        
        tabWrappers.forEach(function (wrapper) {
            const panels = wrapper.querySelectorAll('.dd-tab-panel');
            if (panels.length === 0) return;

            const isAccordionOnMobile = wrapper.getAttribute('data-mobile-accordion') === 'true';

            // 1. Construct the Desktop Navigation Container (Renders Above)
            const desktopNav = document.createElement('div');
            desktopNav.className = 'dd-tabs-nav-desktop';
            wrapper.insertBefore(desktopNav, wrapper.firstChild);

            // Arrays to keep track of generated buttons to sync their active states easily
            const desktopButtons = [];
            const accordionButtons = [];

            /**
             * Centralized logic to activate a specific tab index.
             * * Defined outside the loop so it has access to the fully populated arrays.
             * * @param {number} targetIndex The index of the tab to activate.
             */
            const activateTab = function(targetIndex) {
                panels.forEach((p, i) => {
                    if (i === targetIndex) {
                        // Activate
                        p.classList.add('active');
                        p.style.display = 'block';
                        desktopButtons[i].classList.add('active');
                        desktopButtons[i].setAttribute('aria-selected', 'true');
                        accordionButtons[i].classList.add('active');
                        accordionButtons[i].setAttribute('aria-expanded', 'true');
                    } else {
                        // Deactivate
                        p.classList.remove('active');
                        p.style.display = 'none';
                        desktopButtons[i].classList.remove('active');
                        desktopButtons[i].setAttribute('aria-selected', 'false');
                        accordionButtons[i].classList.remove('active');
                        accordionButtons[i].setAttribute('aria-expanded', 'false');
                    }
                });
            };

            // 2. Loop through panels to create buttons and bind events
            panels.forEach(function (panel, index) {
                const title = panel.getAttribute('data-tab-title') || 'Tab';
                
                // --- Desktop Tab Button ---
                const dBtn = document.createElement('button');
                dBtn.className = 'dd-tab-button';
                dBtn.innerText = title;
                dBtn.setAttribute('role', 'tab');
                desktopNav.appendChild(dBtn);
                desktopButtons.push(dBtn);

                // --- Mobile Accordion Button ---
                const aBtn = document.createElement('button');
                aBtn.className = 'dd-accordion-button';
                aBtn.innerHTML = `<span>${title}</span><span class="dd-accordion-icon"></span>`;
                // Insert the accordion button directly before the panel in the DOM tree
                panel.parentNode.insertBefore(aBtn, panel);
                accordionButtons.push(aBtn);

                // Bind Event Listeners
                dBtn.addEventListener('click', function () { activateTab(index); });
                aBtn.addEventListener('click', function () { 
                    // Accordion toggle logic: If clicking the active accordion, close it. Otherwise, open it.
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

            // 3. Set Initial State (Open first tab by default)
            // Fire this ONLY after the loop has finished and all arrays are populated.
            activateTab(0);
        });
    }

    initDDTabs();
});