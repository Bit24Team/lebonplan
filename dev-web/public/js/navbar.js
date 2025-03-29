document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.querySelector('.sidebar');
  const menuButton = document.querySelector('.menu-button');
  const overlay = document.querySelector('.overlay');

  // Version plus légère des fonctions
  window.showSidebar = () => {
      sidebar.classList.add('active');
      overlay.classList.add('active');
  };

  window.hideSidebar = () => {
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
  };
});