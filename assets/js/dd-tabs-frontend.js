/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Handles the frontend interactivity for the Advanced Tabs block.
 */
document.addEventListener('DOMContentLoaded', function () {
    
    /**
     * Initializes all Tab blocks present on the page.
     * * Iterates through each wrapper, extracts panel data, and constructs the navigation UI.
     * * @return {void}
     */
    function initDDTabs() {
        const tabWrappers = document.querySelectorAll('.dd-tabs-wrapper');
        
        tabWrappers.forEach(function (wrapper) {
            const panels = wrapper.querySelectorAll('.dd-tab-panel');
            if (panels.length === 0) return;

            // Construct the navigation container
            const nav = document.createElement('div');
            nav.className = 'dd-tabs-nav';
            wrapper.insertBefore(nav, wrapper.firstChild);

            panels.forEach(function (panel, index) {
                const title = panel.getAttribute('data-tab-title') || 'Tab';
                
                // Construct individual tab buttons
                const btn = document.createElement('button');
                btn.className = 'dd-tab-button';
                btn.innerText = title;
                btn.setAttribute('role', 'tab');
                
                // Set initial active state
                if (index === 0) {
                    btn.classList.add('active');
                    btn.setAttribute('aria-selected', 'true');
                    panel.classList.add('active');
                } else {
                    btn.setAttribute('aria-selected', 'false');
                    panel.style.display = 'none';
                }

                /**
                 * Click event listener to handle tab switching logic.
                 */
                btn.addEventListener('click', function () {
                    // Reset all buttons and panels in this specific wrapper
                    wrapper.querySelectorAll('.dd-tab-button').forEach(b => {
                        b.classList.remove('active');
                        b.setAttribute('aria-selected', 'false');
                    });
                    wrapper.querySelectorAll('.dd-tab-panel').forEach(p => {
                        p.classList.remove('active');
                        p.style.display = 'none';
                    });
                    
                    // Activate the clicked target
                    btn.classList.add('active');
                    btn.setAttribute('aria-selected', 'true');
                    panel.classList.add('active');
                    panel.style.display = 'block';
                });

                nav.appendChild(btn);
            });
        });
    }

    initDDTabs();
});