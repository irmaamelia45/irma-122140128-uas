### Ticketting

#### **Pengertian Ticketting**

Ticketting adalah sebuah aplikasi berbasis web untuk mengelola pemesanan tiket acara, yang mencakup pendaftaran pengguna, login, pengelolaan tiket, dan penyimpanan data menggunakan backend PHP serta penyajian data melalui frontend HTML, CSS, dan JavaScript.

Link hosting:
http: [ticketting.ct.ws](http://ticketting.ct.ws)
https: [ticketting.ct.ws](https://ticketting.ct.ws)

---

### **Bagian 1: Client-side Programming (30%)**

#### **1.1 Manipulasi DOM dengan JavaScript (15%)**

**Kriteria:**

- Form input dengan minimal 4 elemen (contoh: teks, checkbox, radio).
- Data dari server ditampilkan ke dalam tabel HTML.

**Kode yang Memenuhi:**

1. **Form Input dengan 4 Elemen** terdapat pada `views/dashboard-ticket.php`:

   ```html
   <form
     id="ticketForm"
     method="POST"
     action="../controllers/ticket_controller.php?action=create"
   >
     <label>
       Nama Pemesan:
       <input
         type="text"
         name="name"
         id="name"
         placeholder="Nama pemesan"
         required
       />
     </label>
     <label>
       Email:
       <input
         type="email"
         name="email"
         id="email"
         placeholder="Email pemesan"
         required
       />
     </label>
     <label>
       Jenis Tiket:
       <select name="ticket_type" id="ticket_type" required>
         <option value="VVIP">VVIP</option>
         <option value="VIP">VIP</option>
         <option value="Reguler">Reguler</option>
       </select>
     </label>
     <label>
       Harga:
       <input
         type="number"
         name="price"
         id="price"
         placeholder="Harga tiket"
         required
       />
     </label>
     <button type="submit" id="submitButton">Tambah Tiket</button>
   </form>
   ```

2. **Menampilkan Data dari Server ke Tabel HTML** terdapat pada `public/js/ticket.js`:
   ```javascript
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
   }
   ```

---

#### **1.2 Event Handling (15%)**

**Kriteria:**

- Minimal 3 event untuk meng-handle form.
- Validasi input dengan JavaScript sebelum diproses oleh PHP.

**Kode yang Memenuhi:**

1. **Event Handling** terdapat pada `public/js/ticket.js`:

   - Event untuk validasi saat mengetik di form:
     ```javascript
     nameInput.addEventListener("input", checkFormValidity);
     emailInput.addEventListener("input", checkFormValidity);
     priceInput.addEventListener("input", checkFormValidity);
     ```
   - Event untuk menangani pengiriman form:

     ```javascript
     ticketForm.addEventListener("submit", function (e) {
       e.preventDefault();
       const formData = new URLSearchParams({
         name: nameInput.value.trim(),
         email: emailInput.value.trim(),
         ticket_type: ticketTypeInput.value,
         price: priceInput.value.trim(),
         user_id: sessionStorage.getItem("user_id") || "",
       });

       fetch(`../controllers/ticketting_controller.php?action=${action}`, {
         method: "POST",
         body: formData,
       })
         .then((response) => response.json())
         .then((data) => {
           alert(data.message || data.error);
         });
     });
     ```

2. **Validasi Input** terdapat pada `public/js/ticket.js`:

   ```javascript
   function validateName(name) {
     return name.trim().length >= 3;
   }

   function validateEmail(email) {
     const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
     return emailRegex.test(email);
   }

   function validatePrice(price) {
     return price > 0;
   }

   function checkFormValidity() {
     const isNameValid = validateName(nameInput.value);
     const isEmailValid = validateEmail(emailInput.value);
     const isPriceValid = validatePrice(priceInput.value);

     nameError.textContent = isNameValid
       ? ""
       : "Nama harus minimal 3 karakter.";
     emailError.textContent = isEmailValid ? "" : "Masukkan email yang valid.";
     priceError.textContent = isPriceValid ? "" : "Harga harus lebih dari 0.";

     submitButton.disabled = !(isNameValid && isEmailValid && isPriceValid);
   }
   ```

---

### **Bagian 2: Server-side Programming (30%)**

---

#### **2.1 Pengelolaan Data dengan PHP (20%)**

**Kriteria:**

- Penggunaan metode POST/GET pada formulir.
- Validasi data dari variabel global (`$_POST` atau `$_GET`) di sisi server.
- Penyimpanan data ke basis data, termasuk informasi jenis browser dan alamat IP pengguna.

**Kode yang Memenuhi:**

1. **Penggunaan Metode POST untuk Penyimpanan Data Pengguna** terdapat pada `controllers/process_user.php`:

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       $username = htmlspecialchars($_POST['username']);
       $email = htmlspecialchars($_POST['email']);
       $password = htmlspecialchars($_POST['password']);
       $confirmPassword = htmlspecialchars($_POST['confirm_password']);
       $ip = $_SERVER['REMOTE_ADDR'];
       $browser = $_SERVER['HTTP_USER_AGENT'];

       if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           die("Email tidak valid.");
       }

       if ($password !== $confirmPassword) {
           die("Password dan konfirmasi password tidak cocok.");
       }

       $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

       $stmt = $conn->prepare("INSERT INTO users (name, email, password, ip_address, browser) VALUES (?, ?, ?, ?, ?)");
       $stmt->bind_param("sssss", $username, $email, $hashedPassword, $ip, $browser);

       if ($stmt->execute()) {
           echo "Data berhasil disimpan.";
       } else {
           echo "Gagal menyimpan data: " . $stmt->error;
       }
   }
   ```

2. **Validasi Input dari Formulir di Server** terdapat pada `controllers/ticketting_controller.php`:

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
       $id = generateUuid();
       $name = htmlspecialchars($_POST['name']);
       $email = htmlspecialchars($_POST['email']);
       $ticket_type = htmlspecialchars($_POST['ticket_type']);
       $price = htmlspecialchars($_POST['price']);
       $user_id = htmlspecialchars($_POST['user_id']);

       if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           die("Email tidak valid.");
       }

       if (empty($name) || empty($ticket_type) || $price <= 0) {
           die("Data tiket tidak valid.");
       }

       $stmt = $conn->prepare("INSERT INTO ticket_bookings (id, user_id, name, email, ticket_type, price, booking_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
       $stmt->bind_param("sssssd", $id, $user_id, $name, $email, $ticket_type, $price);

       if ($stmt->execute()) {
           echo json_encode(["message" => "Pemesanan tiket berhasil ditambahkan!", "id" => $id]);
       } else {
           echo json_encode(["error" => "Gagal menambahkan pemesanan tiket."]);
       }
   }
   ```

3. **Penyimpanan Alamat IP dan Jenis Browser** terdapat pada `controllers/process_user.php`:

   ```php
   $ip = $_SERVER['REMOTE_ADDR'];
   $browser = $_SERVER['HTTP_USER_AGENT'];

   $stmt = $conn->prepare("INSERT INTO users (name, email, password, ip_address, browser) VALUES (?, ?, ?, ?, ?)");
   $stmt->bind_param("sssss", $username, $email, $hashedPassword, $ip, $browser);
   ```

---

#### **2.2 Objek PHP Berbasis OOP (10%)**

**Kriteria:**

- Objek PHP berbasis OOP dengan minimal dua metode.
- Objek digunakan dalam skenario tertentu.

**Kode yang Memenuhi:**

1. **Definisi Objek PHP dengan Dua Metode** terdapat pada `models/User.php`:

   ```php
   class User
   {
       private $conn;

       public function __construct($db)
       {
           $this->conn = $db;
       }

       public function findByEmail($email)
       {
           $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
           $stmt->bind_param("s", $email);
           $stmt->execute();

           $result = $stmt->get_result();
           if ($result->num_rows === 0) {
               return null;
           }

           $user = $result->fetch_assoc();
           $stmt->close();

           return $user;
       }

       public function verifyPassword($inputPassword, $storedHash)
       {
           return password_verify($inputPassword, $storedHash);
       }
   }
   ```

2. **Penggunaan Objek dalam Skenario Login** terdapat pada `controllers/user_controller.php`:

   ```php
   $userModel = new User($conn);
   $user = $userModel->findByEmail($email);

   if ($user && $userModel->verifyPassword($password, $user['password'])) {
       $_SESSION['user_id'] = $user['id'];
       $_SESSION['user_name'] = $user['name'];
       $_SESSION['ip_address'] = $user['ip_address'];
       $_SESSION['browser'] = $user['browser'];
       $_SESSION['is_logged_in'] = true;

       echo json_encode(["success" => "Login berhasil.", "redirect" => "dashboard-ticket.php"]);
   } else {
       echo json_encode(["error" => "Email atau password salah."]);
   }
   ```

---

### **Bagian 3: Database Management (20%)**

---

#### **3.1 Pembuatan Tabel Database (5%)**

**Kriteria:**

- Tabel dibuat sesuai kebutuhan aplikasi untuk menyimpan data pengguna dan pemesanan tiket.

**Kode yang Memenuhi:**

1. **Definisi Tabel Database** terdapat pada `config/tiket-konser.sql`:

   ```sql
   CREATE TABLE users (
       id CHAR(36) PRIMARY KEY,
       name VARCHAR(255) NOT NULL,
       email VARCHAR(255) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       ip_address VARCHAR(45),
       browser VARCHAR(255),
       created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
       updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   );

   CREATE TABLE ticket_bookings (
       id CHAR(36) PRIMARY KEY,
       user_id CHAR(36) NOT NULL,
       name VARCHAR(255) NOT NULL,
       email VARCHAR(255) NOT NULL,
       ticket_type ENUM('VVIP', 'VIP', 'Reguler') NOT NULL,
       price DECIMAL(10, 2) NOT NULL,
       booking_date DATETIME DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id)
           ON DELETE CASCADE
           ON UPDATE CASCADE
   );
   ```

   - **Tabel `users`** digunakan untuk menyimpan informasi pengguna, termasuk alamat IP dan jenis browser.
   - **Tabel `ticket_bookings`** digunakan untuk menyimpan data pemesanan tiket, dengan hubungan ke tabel `users` menggunakan `user_id`.

---

#### **3.2 Konfigurasi Koneksi Database (5%)**

**Kriteria:**

- Implementasi koneksi database dengan pengaturan yang benar.

**Kode yang Memenuhi:**

1. **Koneksi Database** terdapat pada `config/db_config.php`:

   ```php
   <?php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "pemweb-uas-irma";

   $conn = new mysqli($servername, $username, $password, $dbname);

   if ($conn->connect_error) {
       die("Koneksi gagal: " . $conn->connect_error);
   }
   ?>
   ```

   - Menggunakan `mysqli` untuk membuat koneksi ke database.
   - Parameter koneksi meliputi:
     - `servername`: Nama server database (default `localhost`).
     - `username` dan `password`: Kredensial pengguna database.
     - `dbname`: Nama database (`pemweb-uas-irma`).

---

#### **3.3 Manipulasi Data pada Database (10%)**

**Kriteria:**

- Implementasi CRUD (Create, Read, Update, Delete) data pada database.

**Kode yang Memenuhi:**

1. **Create** data pengguna (registrasi) pada `controllers/process_user.php`:

   ```php
   $stmt = $conn->prepare("INSERT INTO users (name, email, password, ip_address, browser) VALUES (?, ?, ?, ?, ?)");
   $stmt->bind_param("sssss", $username, $email, $hashedPassword, $ip, $browser);
   $stmt->execute();
   ```

2. **Read** data pemesanan tiket pada `controllers/ticketting_controller.php`:

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetch') {
       $query = "SELECT ticket_bookings.id, ticket_bookings.name, ticket_bookings.email, ticket_bookings.ticket_type,
                 ticket_bookings.price, ticket_bookings.booking_date, users.name AS booked_by
                 FROM ticket_bookings
                 INNER JOIN users ON ticket_bookings.user_id = users.id
                 ORDER BY ticket_bookings.booking_date DESC";
       $result = $conn->query($query);
   }
   ```

3. **Update** data pemesanan tiket pada `controllers/ticketting_controller.php`:

   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
       $stmt = $conn->prepare("UPDATE ticket_bookings SET name = ?, email = ?, ticket_type = ?, price = ? WHERE id = ?");
       $stmt->bind_param("sssds", $name, $email, $ticket_type, $price, $id);
       $stmt->execute();
   }
   ```

4. **Delete** data pemesanan tiket pada `controllers/ticketting_controller.php`:
   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
       $stmt = $conn->prepare("DELETE FROM ticket_bookings WHERE id = ?");
       $stmt->bind_param("s", $id);
       $stmt->execute();
   }
   ```

---

### **Bagian 4: State Management (20%)**

---

#### **4.1 State Management dengan Session (10%)**

**Kriteria:**

- Memulai sesi menggunakan `session_start()`.
- Menyimpan informasi pengguna ke dalam session.

**Kode yang Memenuhi:**

1. **Memulai Sesi dan Menyimpan Informasi Pengguna di Session** terdapat pada `controllers/user_controller.php`:

   ```php
   session_start();

   $userModel = new User($conn);
   $user = $userModel->findByEmail($email);

   if ($user && $userModel->verifyPassword($password, $user['password'])) {
       // Simpan data user ke session
       $_SESSION['user_id'] = $user['id'];
       $_SESSION['user_name'] = $user['name'];
       $_SESSION['ip_address'] = $user['ip_address'];
       $_SESSION['browser'] = $user['browser'];
       $_SESSION['is_logged_in'] = true;

       echo json_encode(["success" => "Login berhasil.", "redirect" => "dashboard-ticket.php"]);
   }
   ```

2. **Validasi Login dengan Session** terdapat pada `session/session.php`:

   ```php
   session_start();
   if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
       header("Location: ../views/login.php");
       exit;
   }

   $userName = $_SESSION['user_name'];
   ```

3. **Menghapus Session saat Logout** terdapat pada `controllers/logout.php`:
   ```php
   session_start();
   session_unset();
   session_destroy();
   header("Location: ../views/login.php");
   exit;
   ```

---

#### **4.2 Pengelolaan State dengan Cookie dan Browser Storage (10%)**

**Kriteria:**

- Fungsi untuk menetapkan, mendapatkan, dan menghapus cookie.
- Penggunaan browser storage (localStorage dan sessionStorage) untuk menyimpan informasi secara lokal.

**Kode yang Memenuhi:**

1. **Fungsi Cookie pada `public/js/aplication.js`:**

   - **Menetapkan Cookie:**
     ```javascript
     document.getElementById("setCookie").addEventListener("click", () => {
       const value = cookieInput.value.trim();
       if (!value) {
         alert("Masukkan nilai terlebih dahulu!");
         return;
       }
       document.cookie = `TickettingCookie=${value}; path=/; max-age=3600`; // Berlaku 1 jam
       updateDisplays();
     });
     ```
   - **Menghapus Cookie:**
     ```javascript
     document.getElementById("deleteCookie").addEventListener("click", () => {
       document.cookie = "TickettingCookie=; path=/; max-age=0";
       updateDisplays();
     });
     ```

2. **Penggunaan Browser Storage pada `public/js/aplication.js`:**

   - **Menetapkan LocalStorage:**
     ```javascript
     document
       .getElementById("setLocalStorage")
       .addEventListener("click", () => {
         const value = localStorageInput.value.trim();
         if (!value) {
           alert("Masukkan nilai terlebih dahulu!");
           return;
         }
         localStorage.setItem("TickettingLocalStorage", value);
         updateDisplays();
       });
     ```
   - **Menghapus LocalStorage:**
     ```javascript
     document
       .getElementById("deleteLocalStorage")
       .addEventListener("click", () => {
         localStorage.removeItem("TickettingLocalStorage");
         updateDisplays();
       });
     ```
   - **Menetapkan SessionStorage:**
     ```javascript
     document
       .getElementById("setSessionStorage")
       .addEventListener("click", () => {
         const value = sessionStorageInput.value.trim();
         if (!value) {
           alert("Masukkan nilai terlebih dahulu!");
           return;
         }
         sessionStorage.setItem("TickettingSessionStorage", value);
         updateDisplays();
       });
     ```
   - **Menghapus SessionStorage:**
     ```javascript
     document
       .getElementById("deleteSessionStorage")
       .addEventListener("click", () => {
         sessionStorage.removeItem("TickettingSessionStorage");
         updateDisplays();
       });
     ```

3. **Antarmuka Pengelolaan State pada `views/cookie-local-session.html`:**
   - Antarmuka untuk menetapkan, mendapatkan, dan menghapus cookie, localStorage, dan sessionStorage:
     ```html
     <section>
       <h2>Kelola Cookie</h2>
       <form id="cookieForm">
         <label for="cookieInput">Masukkan Nilai Cookie:</label>
         <input
           type="text"
           id="cookieInput"
           placeholder="Masukkan nilai..."
           required
         />
         <button type="button" class="button btn-set" id="setCookie">
           Set Cookie
         </button>
         <button type="button" class="button btn-delete" id="deleteCookie">
           Hapus Cookie
         </button>
       </form>
       <p>
         <strong>Nilai Cookie:</strong>
         <span id="cookieDisplay">Belum ada nilai</span>
       </p>
     </section>
     ```

---

### **Bagian Bonus: Hosting Aplikasi Web (20%)**

#### **1. Langkah-langkah Meng-host Aplikasi**

1. Siapkan file codingan yang sudah siap untuk diunggah.
2. Login ke akun **InfinityFree**.
3. Setup database MySQL di InfinityFree.
4. Buat akun untuk database dengan kredensial yang diberikan InfinityFree.
5. Sesuaikan file `config/db_config.php` dengan konfigurasi database yang disediakan oleh InfinityFree.

#### **2. Pilih Penyedia Hosting**

**InfinityFree** dipilih karena:

- Gratis dan cocok untuk proyek kecil.
- Mendukung PHP dan MySQL yang sesuai dengan aplikasi ini.
- Menyediakan fitur yang cukup untuk pengembangan awal.

#### **3. Keamanan Aplikasi Web**

- **InfinityFree** menggunakan password yang dihasilkan secara acak, meningkatkan keamanan akun hosting.
- Mendukung **HTTPS** untuk koneksi yang aman.

#### **4. Konfigurasi Server**

- Pastikan konfigurasi database di **InfinityFree** sesuai dengan aplikasi, termasuk nama database, username, dan password.
- File utama seperti `index.php` harus ditempatkan di direktori `htdocs` atau di root agar aplikasi dapat diakses dengan benar.
