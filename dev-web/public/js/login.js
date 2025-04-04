const container = document.querySelector(".container");
const registerBtn = document.querySelector(".register-btn");
const loginBtn = document.querySelector(".login-btn");

registerBtn.addEventListener("click", () => {
  container.classList.add("active");
});

loginBtn.addEventListener("click", () => {
  container.classList.remove("active");
});

document.querySelector('form').addEventListener('submit', function(e) {
  e.preventDefault();
  console.log('Form submitted');
  // Vérifiez les valeurs des champs
  console.log('Email:', document.querySelector('input[name="email"]').value);
  console.log('Password:', document.querySelector('input[name="password"]').value);
  // Soumettez le formulaire
  this.submit();
});