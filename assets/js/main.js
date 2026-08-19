document.addEventListener("DOMContentLoaded", function () {
    const themeSwitch = document.querySelector('#theme-toggler input[type="checkbox"]');
    const themeIcon = document.querySelector('#theme-toggler label i');
    const body = document.body;

    // Function to set the theme based on the stored preference or system preference
    const applyTheme = () => {
        // Get the saved theme, or default to 'light'
        const currentTheme = localStorage.getItem('theme') || 'light';

        if (currentTheme === 'dark') {
            body.classList.add('dark-mode');
            themeSwitch.checked = true;
            if(themeIcon) {
                themeIcon.classList.remove('bi-sun-fill');
                themeIcon.classList.add('bi-moon-stars-fill');
            }
        } else {
            body.classList.remove('dark-mode');
            themeSwitch.checked = false;
            if(themeIcon) {
                themeIcon.classList.remove('bi-moon-stars-fill');
                themeIcon.classList.add('bi-sun-fill');
            }
        }
    };

    // Listen for a change on the switch
    if(themeSwitch) {
        themeSwitch.addEventListener('change', function() {
            let theme;
            if (this.checked) {
                body.classList.add('dark-mode');
                theme = 'dark';
                if(themeIcon) {
                    themeIcon.classList.remove('bi-sun-fill');
                    themeIcon.classList.add('bi-moon-stars-fill');
                }
            } else {
                body.classList.remove('dark-mode');
                theme = 'light';
                if(themeIcon) {
                    themeIcon.classList.remove('bi-moon-stars-fill');
                    themeIcon.classList.add('bi-sun-fill');
                }
            }
            // Save the user's preference to localStorage
            localStorage.setItem('theme', theme);
        });
    }

    // Apply the theme when the page loads
    applyTheme();
});