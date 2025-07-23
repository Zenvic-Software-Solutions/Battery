document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.querySelector(".light-dark-mode");
    const icon = toggleBtn?.querySelector("i");
    const html = document.documentElement;

    if (!toggleBtn || !icon) {
        console.warn("Theme toggle button or icon not found.");
        return;
    }

    function setIcon(theme) {
        if (theme === "dark") {
            icon.classList.remove("bx-moon");
            icon.classList.add("bx-sun");
        } else {
            icon.classList.remove("bx-sun");
            icon.classList.add("bx-moon");
        }
    }

    let currentTheme = localStorage.getItem("themeMode") || "light";
    html.setAttribute("data-bs-theme", currentTheme);
    setIcon(currentTheme);

    toggleBtn.addEventListener("click", function () {
        currentTheme = currentTheme === "dark" ? "light" : "dark";
        html.setAttribute("data-bs-theme", currentTheme);
        localStorage.setItem("themeMode", currentTheme);
        setIcon(currentTheme);
    });
});
