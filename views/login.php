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
  <title>Login - Ticketting</title>
  <link rel="stylesheet" href="../public/css/style.css" />
  <script src="../public/js/login.js" defer></script>
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
      <h1>Login Akun</h1>

      <form id="loginForm" method="POST" action="../process/process_login.php" class="form-container">
        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required />
        <span id="emailError" class="error"></span>

        <!-- Password -->
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required />
        <span id="passwordError" class="error"></span>

        <!-- Submit Button -->
        <button type="submit" class="cta-button">Masuk</button>
      </form>
    </section>
  </main>

  <footer class="footer">
    <p>&copy; Copyright Ticketting 2024</p>
  </footer>
</body>

</html>