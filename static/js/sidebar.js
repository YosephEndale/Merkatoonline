const sidebarToggleBtn = document.querySelector('.toggle-btn');
const sidebarNav = document.querySelector('.sidenav');
if (sidebarToggleBtn && sidebarNav) {
    sidebarToggleBtn.addEventListener('click', function() {
        sidebarNav.classList.toggle('expanded');
    });
}
