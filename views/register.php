<?php
session_start();

if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']) {
  header("Location: dashboard-ticket.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Ticketting</title>
  <link rel="stylesheet" href="../public/css/style.css" />
  <script src="../public/js/register.js" defer></script>
</head>

<body>
  <header class="header">
    <div class="container">
      <a href="../index.php" class="logo">Ticketing</a>
      <p>Platform untuk memesan tiket acara favorit Anda</p>
    </div>
  </header>

  <nav class="navigation">
    <ul>
      <li><a href="register.php">Daftar</a></li>
      <li><a href="login.php">Masuk</a></li>
      <li><a href="dashboard-ticket.php">Dashboard</a></li>
    </ul>
  </nav>

  <main class="main-content">
    <section class="form-section">
      <h1>Daftar Akun</h1>

      <form id="userForm" method="POST" action="../controllers/process_user.php" class="form-container">
        <!-- Username -->
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required />
        <span id="usernameError" class="error"></span>

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required />
        <span id="emailError" class="error"></span>

        <!-- Password -->
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required />
        <span id="passwordError" class="error"></span>

        <!-- Konfirmasi Password -->
        <label for="confirm_password">Konfirmasi Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required />
        <span id="confirmPasswordError" class="error"></span>

        <!-- Terms and Conditions -->
        <div class="checkbox-container">
          <input type="checkbox" id="terms" required />
          <span>Saya setuju dengan syarat dan ketentuan</span>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="cta-button">Daftar</button>
      </form>
    </section>
  </main>

  <footer class="footer">
    <p>&copy; Copyright Ticketting 2024</p>
  </footer>
</body>

</html>