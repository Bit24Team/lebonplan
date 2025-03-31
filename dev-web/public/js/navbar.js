document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector(".sidebar");
  const menuButton = document.querySelector(".menu-button");
  const overlay = document.querySelector(".overlay");

  sidebar.style.display = "none";
  overlay.style.display = "none";

  window.showSidebar = () => {
    menuButton.style.opacity = "0";
    menuButton.style.pointerEvents = "none";

    sidebar.style.display = "flex";
    overlay.style.display = "block";

    void sidebar.offsetWidth;

    setTimeout(() => {
      sidebar.classList.add("active");
      overlay.classList.add("active");
    }, 10);
  };

  window.hideSidebar = () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");

    menuButton.style.opacity = "1";
    menuButton.style.pointerEvents = "auto";

    setTimeout(() => {
      sidebar.style.display = "none";
      overlay.style.display = "none";
    }, 300);
  };
});
