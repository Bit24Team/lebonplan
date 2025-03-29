document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector(".sidebar");
  const menuButton = document.querySelector(".menu-button");
  const overlay = document.querySelector(".overlay");

  // Cache initial
  sidebar.style.display = "none";
  overlay.style.display = "none";

  window.showSidebar = () => {
    menuButton.style.opacity = "0";
    menuButton.style.pointerEvents = "none";

    sidebar.style.display = "flex";
    overlay.style.display = "block";

    requestAnimationFrame(() => {
      sidebar.classList.add("active");
      overlay.classList.add("active");
    });
  };

  window.hideSidebar = () => {
    sidebar.style.display = "none";
    overlay.style.display = "none";

    // Réaffiche instantanément le burger
    menuButton.style.opacity = "1";
    menuButton.style.pointerEvents = "auto";

    sidebar.classList.remove("active");
    overlay.classList.remove("active");
  };
});
