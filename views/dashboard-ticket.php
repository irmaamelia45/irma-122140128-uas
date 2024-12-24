<?php
session_start();
include '../config/db_config.php';

// Periksa apakah pengguna telah login
if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemesanan Tiket</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <script src="../public/js/ticket.js" defer></script>

    <style>
        /* Styling untuk elemen select */
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #8e24aa;
            /* Ungu tua */
            border-radius: 5px;
            background-color: #f3e5f5;
            /* Ungu muda */
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #4a148c;
            /* Ungu gelap */
            appearance: none;
            /* Hilangkan default arrow browser */
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDE2IDE2Ij48cGF0aCBmaWxsPSIjNGExNDhjIiBkPSJNNC40MTUgNi41OTFsNCAzLjgyNGMyLjQxNiAyLjMxMyA1LjM3OC0uNTkxIDUuMzc4LTIuOTAzTDguOTIzIDQuNDg5YTIuNzM2IDIuNzM2IDAgMCAwLTQuNDY3IDBMMTQuMTM2IDMuNzg1YTIuNzM3IDIuNzM3IDAgMCAwIDAgNC45MjNsLTQgMy44MjQgMi40MTYgMi4zMTMgNS4zNzgtLjU5MSA1LjM3OC0uNTkxTDcuNTY2IDEyLjg3YTQuNzkyIDQuNzkyIDAgMCAxLTcuMTg3IDAgTS02LjcyNiA2LjU5bC00LTMuODI0YTIuNzM3IDIuNzM3IDAgMCAxIDAtNC45MjNsNC4xNzUgMi40MTZ2LTMuMjU2YTIuNzM2IDIuNzM2IDAgMCAwLTQuNDY3IDBMMTYuMTYgMy43ODVhMi43MzYgMi43MzYgMCAwIDAgMC00LjkyM0wyLjgzMyA1LjQ1OGEyLjc2MiAyLjc2MiAwIDAgMSAwIDQuOTIzbC00LTMuODI0YTIuNzM3IDIuNzM3IDAgMCAxIDAtNC45MjNsNC4xNzUgMi40MTZ2LTMuMjU2YTIuNzM2IDIuNzM2IDAgMCAwLTQuNDY3IDBMNC4zNjYgMy43ODVhMi43MzYgMi43MzYgMCAwIDAgMC00LjkyM0w0LjYyNiAzLjc4NWMyLjQxNiAyLjMxMyA1LjM3OC0uNTkxIDUuMzc4LTIuOTAzTDQuNDE1IDYuNTkxWiI+PC9wYXRoPjwvc3ZnPg==');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px;
        }

        select:focus {
            outline: none;
            border-color: #6a1b9a;
            /* Ungu medium */
            box-shadow: 0 0 4px rgba(138, 43, 226, 0.5);
            /* Glow efek */
        }

        label {
            display: block;
            margin-bottom: 15px;
        }

        label input,
        label select {
            margin-top: 5px;
        }
    </style>
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
            <li><a href="dashboard-ticket.php">Dashboard</a></li>
            <li><a href="cookie-local-session.html">Coba Session, Cookie, Local</a></li>
            <li><a href="../controllers/logout.php" class="logout-button">Logout</a></li>
        </ul>
    </nav>

    <main>
        <section class="hero">
            <h1>Selamat Datang di Dashboard Pemesanan Tiket</h1>
            <p>Kelola pemesanan tiket yang telah Anda buat.</p>

            <section class="user-info"
                style="padding: 20px; max-width: 700px; border: 1px solid #ccc; border-radius: 10px;">
                <h2 style="margin-bottom: 20px; text-align: center;">Informasi Pengguna</h2>
                <div style="margin-bottom: 15px;">
                    <strong>Nama Pengguna:</strong>
                    <p><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>IP Address:</strong>
                    <p><?php echo htmlspecialchars($_SESSION['ip_address']); ?></p>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Browser:</strong>
                    <p><?php echo htmlspecialchars($_SESSION['browser']); ?></p>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Cookie PHPSESSID:</strong>
                    <p>
                        <?php
                        echo isset($_COOKIE['PHPSESSID']) ? htmlspecialchars($_COOKIE['PHPSESSID']) : 'Tidak ada cookie PHPSESSID.';
                        ?>
                    </p>
                </div>
            </section>
        </section>

        <section class="dashboard-form">
            <h2>Formulir Pemesanan Tiket</h2>

            <form id="ticketForm" method="POST" action="../controllers/ticket_controller.php?action=create">
                <label>
                    Nama Pemesan:
                    <input type="text" name="name" id="name" placeholder="Nama pemesan" required>
                </label>
                <label>
                    Email:
                    <input type="email" name="email" id="email" placeholder="Email pemesan" required>
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
                    <input type="number" name="price" id="price" placeholder="Harga tiket" required>
                </label>
                <button type="submit" id="submitButton">Tambah Tiket</button>
            </form>
        </section>

        <section class="dashboard-table">
            <h2>Daftar Pemesanan Tiket</h2>
            <table id="ticketTable">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jenis Tiket</th>
                        <th>Harga</th>
                        <th>Tanggal Pemesanan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="noDataRow">
                        <td colspan="6" style="text-align:center;">Tidak ada data tiket tersedia.</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>
