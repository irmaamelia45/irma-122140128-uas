document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("userForm");
  const usernameInput = document.getElementById("username");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirm_password");
  const termsCheckbox = document.getElementById("terms");
  const submitButton = form.querySelector("button[type='submit']");

  // Referensi elemen error yang sudah ada di HTML
  const usernameError = document.getElementById("usernameError");
  const emailError = document.getElementById("emailError");
  const passwordError = document.getElementById("passwordError");
  const confirmPasswordError = document.getElementById("confirmPasswordError");

  // Validasi input
  function validateUsername(username) {
    return username.trim().length >= 3;
  }

  function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  function validatePassword(password) {
    return password.trim().length >= 8;
  }

  function passwordsMatch(password, confirmPassword) {
    return password === confirmPassword;
  }

  function checkFormValidity() {
    const isUsernameValid = validateUsername(usernameInput.value);
    const isEmailValid = validateEmail(emailInput.value);
    const isPasswordValid = validatePassword(passwordInput.value);
    const isConfirmPasswordValid = passwordsMatch(
      passwordInput.value,
      confirmPasswordInput.value
    );
    const isTermsChecked = termsCheckbox.checked;

    // Pesan error
    usernameError.textContent = isUsernameValid
      ? ""
      : "Username minimal 3 karakter.";
    emailError.textContent = isEmailValid ? "" : "Masukkan email yang valid.";
    passwordError.textContent = isPasswordValid
      ? ""
      : "Password minimal 8 karakter.";
    confirmPasswordError.textContent = isConfirmPasswordValid
      ? ""
      : "Password dan konfirmasi password harus sama.";

    // Tombol submit hanya aktif jika semua validasi lolos
    submitButton.disabled = !(
      isUsernameValid &&
      isEmailValid &&
      isPasswordValid &&
      isConfirmPasswordValid &&
      isTermsChecked
    );
  }

  // Event listeners untuk validasi
  usernameInput.addEventListener("input", checkFormValidity);
  emailInput.addEventListener("input", checkFormValidity);
  passwordInput.addEventListener("input", checkFormValidity);
  confirmPasswordInput.addEventListener("input", checkFormValidity);
  termsCheckbox.addEventListener("change", checkFormValidity);

  // Submit form
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch("../controllers/process_user.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (response.ok) {
          return response.text();
        } else {
          throw new Error("Gagal menyimpan data.");
        }
      })
      .then((data) => {
        alert(data);
        window.location.href = "../views/login.php";
      })
      .catch((error) => {
        alert(error.message);
      });
  });

  // Inisialisasi: disable tombol submit saat pertama kali load
  submitButton.disabled = true;
});
