document.addEventListener("DOMContentLoaded", () => {
  const cookieInput = document.getElementById("cookieInput");
  const localStorageInput = document.getElementById("localStorageInput");
  const sessionStorageInput = document.getElementById("sessionStorageInput");

  const cookieDisplay = document.getElementById("cookieDisplay");
  const localStorageDisplay = document.getElementById("localStorageDisplay");
  const sessionStorageDisplay = document.getElementById(
    "sessionStorageDisplay"
  );

  function updateDisplays() {
    const cookies = document.cookie.split("; ").reduce((acc, cookie) => {
      const [key, value] = cookie.split("=");
      acc[key] = value;
      return acc;
    }, {});
    cookieDisplay.textContent = cookies.TickettingCookie || "Belum ada nilai";

    localStorageDisplay.textContent =
      localStorage.getItem("TickettingLocalStorage") || "Belum ada nilai";

    sessionStorageDisplay.textContent =
      sessionStorage.getItem("TickettingSessionStorage") || "Belum ada nilai";
  }

  document.getElementById("setCookie").addEventListener("click", () => {
    const value = cookieInput.value.trim();
    if (!value) {
      alert("Masukkan nilai terlebih dahulu!");
      return;
    }
    document.cookie = `TickettingCookie=${value}; path=/; max-age=3600`; // Berlaku 1 jam
    updateDisplays();
  });

  document.getElementById("deleteCookie").addEventListener("click", () => {
    document.cookie = "TickettingCookie=; path=/; max-age=0";
    updateDisplays();
  });

  document.getElementById("setLocalStorage").addEventListener("click", () => {
    const value = localStorageInput.value.trim();
    if (!value) {
      alert("Masukkan nilai terlebih dahulu!");
      return;
    }
    localStorage.setItem("TickettingLocalStorage", value);
    updateDisplays();
  });

  document
    .getElementById("deleteLocalStorage")
    .addEventListener("click", () => {
      localStorage.removeItem("TickettingLocalStorage");
      updateDisplays();
    });

  document.getElementById("setSessionStorage").addEventListener("click", () => {
    const value = sessionStorageInput.value.trim();
    if (!value) {
      alert("Masukkan nilai terlebih dahulu!");
      return;
    }
    sessionStorage.setItem("TickettingSessionStorage", value);
    updateDisplays();
  });

  document
    .getElementById("deleteSessionStorage")
    .addEventListener("click", () => {
      sessionStorage.removeItem("TickettingSessionStorage");
      updateDisplays();
    });

  updateDisplays();
});
