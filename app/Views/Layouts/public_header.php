<?php
$bodyClass = trim('hirematrix-app public-header-page ' . ($body_class ?? ''));
?> 
<style>
    .theme-toggle-btn{
    width: 42px;
    height: 42px;

    border: none;
    outline: none;
    cursor: pointer;

    border-radius: 50%;
    background: rgba(255,255,255,0.12);

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 20px;

    transition: all 0.3s ease;
}

.theme-toggle-btn:hover{
    transform: rotate(15deg) scale(1.08);
}

.theme-toggle-btn:focus{
    outline: none;
    box-shadow: none;
}
    </style>
<body id="top" class="<?= esc($bodyClass) ?>">
<div id="overlayer"></div>
<div class="loader">
    <div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>
</div>

<div class="site-wrap">
    <div class="site-mobile-menu site-navbar-target">
        <div class="site-mobile-menu-header">
            <div class="site-mobile-menu-close mt-3">
                <span class="icon-close2 js-menu-toggle"></span>
            </div>
        </div>
        <div class="site-mobile-menu-body"></div>
    </div>

    <header class="site-navbar site-navbar-target landing-header">
        <div class="container-fluid">
            <div class="row align-items-center landing-header-row">
                <div class="site-logo col-auto">
                    <a href="<?= site_url('/') ?>" class="d-inline-flex align-items-center landing-header-logo-link" aria-label="Go to landing page">
                        <img src="<?= base_url('jobboard/images/Serp Hwak Logo.png') ?>" alt="HireMatrix Logo" class="landing-header-logo-image">
                        <span class="landing-header-logo-text" style="text-transform: none;">HireMatrix</span>
                    </a>
                </div>
                <nav class="mx-auto site-navigation col-xl">
                    <ul class="site-menu js-clone-nav d-none d-lg-flex ml-0 pl-0 landing-header-nav">
                        <li><a href="<?= base_url('register') ?>">Register Candidate</a></li>
                        <li><a href="<?= base_url('recruiter/register') ?>">Register Recruiter</a></li>
                        <li class="d-lg-none border-top mt-2 pt-2"><a href="<?= site_url('login') ?>" class="text-primary fw-bold">Sign In</a></li>
                    </ul>
                </nav>
                <div class="right-cta-menu text-right d-flex justify-content-end align-items-center col-auto landing-header-actions">
                   <button id="themeToggle" class="theme-toggle-btn" aria-label="Toggle Theme">
    🌙
</button>
                    <a href="<?= site_url('login') ?>" class="btn btn-primary btn-sm landing-header-cta d-none d-lg-inline-flex" role="button">Sign In</a>
                    <a href="#" class="site-menu-toggle js-menu-toggle d-inline-block d-lg-none mt-lg-2 ml-3">
                        <span class="icon-menu h3 m-0 p-0 mt-2"></span>
                    </a>
                </div>
            </div>
        </div>
    </header>
 
   <script>
const toggleBtn = document.getElementById("themeToggle");
const darkThemeId = "dark-theme-css";

/* ================= FORCE LIGHT FIRST ================= */
document.body.classList.remove("dark");

/* ================= LOAD DARK CSS ================= */
function loadDarkTheme() {
    if (!document.getElementById(darkThemeId)) {
        const link = document.createElement("link");
        link.id = darkThemeId;
        link.rel = "stylesheet";
        link.href = "<?= base_url('jobboard/css/dark.css') ?>"; // ✅ correct path
        document.head.appendChild(link);
    }
}

/* ================= REMOVE DARK CSS ================= */
function removeDarkTheme() {
    const darkCss = document.getElementById(darkThemeId);
    if (darkCss) darkCss.remove();
}

/* ================= ICON ================= */
function updateThemeIcon() {
    if (!toggleBtn) return;

    toggleBtn.innerHTML = document.body.classList.contains("dark")
        ? "☀️"
        : "🌙";
}

/* ================= INITIAL LOAD ================= */
const savedTheme = localStorage.getItem("theme");

/* DEFAULT = LIGHT ALWAYS */
if (savedTheme === "dark") {
    document.body.classList.add("dark");
    loadDarkTheme();
} else {
    document.body.classList.remove("dark");
    removeDarkTheme();
}

/* update icon */
updateThemeIcon();

/* ================= TOGGLE ================= */
if (toggleBtn) {
    toggleBtn.addEventListener("click", function () {

        const isDark = document.body.classList.toggle("dark");

        if (isDark) {
            localStorage.setItem("theme", "dark");
            loadDarkTheme();
        } else {
            localStorage.setItem("theme", "light");
            removeDarkTheme();
        }

        updateThemeIcon();
    });
}
</script>
     