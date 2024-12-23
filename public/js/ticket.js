document.addEventListener("DOMContentLoaded", function () {
  const ticketForm = document.getElementById("ticketForm");
  const nameInput = document.getElementById("name");
  const emailInput = document.getElementById("email");
  const ticketTypeInput = document.getElementById("ticket_type");
  const priceInput = document.getElementById("price");
  const submitButton = ticketForm.querySelector("button[type='submit']");
  const ticketTable = document
    .getElementById("ticketTable")
    .querySelector("tbody");

  const nameError = document.createElement("p");
  const emailError = document.createElement("p");
  const priceError = document.createElement("p");

  nameError.className = "error-message";
  emailError.className = "error-message";
  priceError.className = "error-message";

  nameInput.parentElement.appendChild(nameError);
  emailInput.parentElement.appendChild(emailError);
  priceInput.parentElement.appendChild(priceError);

  let editMode = false;
  let editId = null;

  // Fungsi untuk validasi nama
  function validateName(name) {
    return name.trim().length >= 3;
  }

  // Fungsi untuk validasi email
  function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // Fungsi untuk validasi harga
  function validatePrice(price) {
    return price > 0;
  }

  // Fungsi untuk memeriksa validitas form
  function checkFormValidity() {
    const isNameValid = validateName(nameInput.value);
    const isEmailValid = validateEmail(emailInput.value);
    const isPriceValid = validatePrice(priceInput.value);

    // Tampilkan atau sembunyikan pesan error
    nameError.textContent = isNameValid ? "" : "Nama harus minimal 3 karakter.";
    emailError.textContent = isEmailValid ? "" : "Masukkan email yang valid.";
    priceError.textContent = isPriceValid ? "" : "Harga harus lebih dari 0.";

    // Atur status tombol submit
    submitButton.disabled = !(isNameValid && isEmailValid && isPriceValid);
  }

  // Event listener untuk validasi input
  nameInput.addEventListener("input", checkFormValidity);
  emailInput.addEventListener("input", checkFormValidity);
  priceInput.addEventListener("input", checkFormValidity);

  // Tambahkan baris tiket ke tabel
  function addTicketRow(ticket) {
    const row = document.createElement("tr");
    row.innerHTML = `
          <td>${ticket.name}</td>
          <td>${ticket.email}</td>
          <td>${ticket.ticket_type}</td>
          <td>Rp ${parseFloat(ticket.price).toLocaleString()}</td>
          <td>${ticket.booking_date}</td>
          <td>
            <button class="edit-btn btn-green" data-id="${
              ticket.id
            }">Edit</button>
            <button class="delete-btn btn-red" data-id="${
              ticket.id
            }">Hapus</button>
          </td>
        `;

    ticketTable.appendChild(row);
    attachRowEventListeners(row);
  }

  // Reset tabel
  function resetTable() {
    ticketTable.innerHTML = "";
  }

  // Event listener pada tombol Edit dan Hapus
  function attachRowEventListeners(row) {
    const editBtn = row.querySelector(".edit-btn");
    const deleteBtn = row.querySelector(".delete-btn");

    editBtn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");

      fetch(
        `../controllers/ticketting_controller.php?action=fetchById&id=${id}`
      )
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            alert(data.error);
          } else {
            nameInput.value = data.name;
            emailInput.value = data.email;
            ticketTypeInput.value = data.ticket_type;
            priceInput.value = data.price;

            editMode = true;
            editId = id;
            submitButton.textContent = "Update Tiket";
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Gagal mengambil data tiket.");
        });
    });

    deleteBtn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      if (confirm("Yakin ingin menghapus tiket ini?")) {
        fetch(`../controllers/ticketting_controller.php?action=delete`, {
          method: "POST",
          body: new URLSearchParams({ id }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.error) {
              alert(data.error);
            } else {
              alert(data.message);
              resetTable();
              loadTickets();
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Gagal menghapus tiket.");
          });
      }
    });
  }

  // Submit form
  ticketForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new URLSearchParams({
      name: nameInput.value.trim(),
      email: emailInput.value.trim(),
      ticket_type: ticketTypeInput.value,
      price: priceInput.value.trim(),
      user_id: sessionStorage.getItem("user_id") || "",
    });

    const action = editMode ? "update" : "create";
    if (editMode) {
      formData.append("id", editId);
    }

    fetch(`../controllers/ticketting_controller.php?action=${action}`, {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        if (data.error) {
          alert(data.error);
        } else {
          alert(data.message);
          resetTable();
          loadTickets();
          ticketForm.reset();
          submitButton.textContent = "Tambah Tiket";
          editMode = false;
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert(
          `Terjadi kesalahan saat ${
            editMode ? "memperbarui" : "menambah"
          } tiket.`
        );
      });
  });

  // Muat data tiket dari server
  function loadTickets() {
    fetch("../controllers/ticketting_controller.php?action=fetch", {
      method: "GET",
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then((tickets) => {
        resetTable();
        tickets.forEach((ticket) => addTicketRow(ticket));
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Gagal memuat data tiket.");
      });
  }

  // Inisialisasi
  checkFormValidity();
  loadTickets();
});
