document.addEventListener("DOMContentLoaded", function () {
  const animatedElements = document.querySelectorAll(".card, .mission-item");

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");

          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.1,
    }
  );

  animatedElements.forEach((element) => {
    observer.observe(element);

    element.style.transitionDelay = Math.random() * 0.3 + "s";
  });
});
