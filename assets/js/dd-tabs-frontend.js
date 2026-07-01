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
    /**
     * Builds the two-column "Vertical Stacked" layout:
     * tab content on the left, a vertical list of nav tabs on the right.
     * The active nav tab reveals its description text (a separate field).
     * * @param {Element}   wrapper The .dd-tabs-wrapper element.
     * @param {NodeList}  panels  The child .dd-tab-panel elements.
     * @return {void}
     */
    function buildVerticalTabs(wrapper, panels) {
        // Left column: holds the panel content (+ accordion headers on mobile).
        const contentArea = document.createElement('div');
        contentArea.className = 'dd-tabs-content-area';

        // Right column: holds the vertical nav tabs.
        const navVertical = document.createElement('div');
        navVertical.className = 'dd-tabs-nav-vertical';

        const navButtons = [];
        const accordionButtons = [];

        const setActive = function (i, isActive) {
            panels[i].classList.toggle('active', isActive);
            panels[i].style.display = isActive ? 'block' : 'none';
            navButtons[i].classList.toggle('active', isActive);
            navButtons[i].setAttribute('aria-selected', isActive ? 'true' : 'false');
            accordionButtons[i].classList.toggle('active', isActive);
            accordionButtons[i].setAttribute('aria-expanded', isActive ? 'true' : 'false');
        };

        const activate = function (targetIndex) {
            panels.forEach(function (p, i) { setActive(i, i === targetIndex); });
        };

        panels.forEach(function (panel, index) {
            const title = panel.getAttribute('data-tab-title') || 'Tab';
            const desc = panel.getAttribute('data-tab-description') || '';

            // --- Right-column nav tab (desktop) ---
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'dd-vtab-button';
            btn.setAttribute('role', 'tab');

            const titleEl = document.createElement('span');
            titleEl.className = 'dd-vtab-title';
            titleEl.textContent = title;
            btn.appendChild(titleEl);

            if (desc) {
                const descEl = document.createElement('span');
                descEl.className = 'dd-vtab-desc';
                descEl.textContent = desc;
                btn.appendChild(descEl);

                // Mirror the description inside the panel for the accordion view
                // (the right-hand nav is hidden once collapsed).
                const panelDesc = document.createElement('div');
                panelDesc.className = 'dd-tab-panel-desc';
                panelDesc.textContent = desc;
                panel.insertBefore(panelDesc, panel.firstChild);
            }

            navVertical.appendChild(btn);
            navButtons.push(btn);
            btn.addEventListener('click', function () { activate(index); });

            // --- Accordion header (mobile) ---
            const aBtn = document.createElement('button');
            aBtn.type = 'button';
            aBtn.className = 'dd-accordion-button';
            aBtn.innerHTML = '<span>' + title + '</span><span class="dd-accordion-icon"></span>';
            accordionButtons.push(aBtn);

            // Header sits directly above its panel in the content column.
            contentArea.appendChild(aBtn);
            contentArea.appendChild(panel);

            aBtn.addEventListener('click', function () {
                // Accordion toggle: re-clicking the open header collapses it.
                if (aBtn.classList.contains('active')) {
                    setActive(index, false);
                } else {
                    activate(index);
                }
            });
        });

        // Left first, nav second; CSS arranges them as two columns.
        wrapper.appendChild(contentArea);
        wrapper.appendChild(navVertical);

        activate(0);
    }

    function initDDTabs() {
        const tabWrappers = document.querySelectorAll('.dd-tabs-wrapper');

        tabWrappers.forEach(function (wrapper) {
            const panels = wrapper.querySelectorAll('.dd-tab-panel');
            if (panels.length === 0) return;

            const isAccordionOnMobile = wrapper.getAttribute('data-mobile-accordion') === 'true';
            const layout = wrapper.getAttribute('data-layout') || 'horizontal';

            // Vertical Stacked layout has its own dedicated builder.
            if (layout === 'stacked') {
                buildVerticalTabs(wrapper, panels);
                return;
            }

            // 1. Construct the Desktop Navigation Container. It renders above the
            //    panels by default, or below when data-nav-placement="bottom".
            const desktopNav = document.createElement('div');
            desktopNav.className = 'dd-tabs-nav-desktop';
            if (wrapper.getAttribute('data-nav-placement') === 'bottom') {
                wrapper.appendChild(desktopNav);
            } else {
                wrapper.insertBefore(desktopNav, wrapper.firstChild);
            }

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