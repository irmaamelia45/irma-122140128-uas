<?php
session_start();
include './config/db_config.php';

// Ambil data pemesanan tiket terbaru dari database
$query = "SELECT ticket_bookings.name, ticket_bookings.email, ticket_bookings.ticket_type, ticket_bookings.price, ticket_bookings.booking_date, users.name AS booked_by 
          FROM ticket_bookings 
          INNER JOIN users ON ticket_bookings.user_id = users.id 
          ORDER BY ticket_bookings.booking_date DESC";
$result = $conn->query($query);

// Periksa apakah ada data yang ditemukan
$ticketBookings = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ticketBookings[] = $row;
    }
    // Ambil hanya 3 item pertama
    $ticketBookings = array_slice($ticketBookings, 0, 3);
} else {
    $ticketBookings = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ticketting - Pemesanan Tiket</title>
    <link rel="stylesheet" href="./public/css/style.css" />

    <style>
        .ticket-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .ticket-card {
            background-color: #e8f5e9;
            /* Hijau lembut */
            color: #2e7d32;
            /* Hijau tua */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: left;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .ticket-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .ticket-card p {
            margin: 5px 0;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .ticket-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">Ticketting</a>
            <p>Platform untuk memesan tiket acara favorit Anda</p>
        </div>
    </header>

    <nav class="navigation">
        <ul>
            <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']): ?>
                <li><a href="./views/dashboard-ticket.php">Dashboard</a></li>
                <li><a href="./controllers/logout.php" class="logout-button">Logout</a></li>
            <?php else: ?>
                <li><a href="./views/register.php">Daftar</a></li>
                <li><a href="./views/login.php">Masuk</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main class="main-content">
        <section class="hero">
            <h1>Selamat Datang di Ticketting</h1>
            <p>Pesan tiket acara favorit Anda dengan mudah dan cepat</p>
            <a href="./views/dashboard-ticket.php" class="cta-button">Pesan Tiket</a>
        </section>

        <h2 style="text-align:center;">Pemesanan Terbaru</h2>

        <section class="ticket-list">
            <div class="ticket-container">
                <?php if (!empty($ticketBookings)): ?>
                    <?php foreach ($ticketBookings as $booking): ?>
                        <div class="ticket-card">
                            <h3><?php echo htmlspecialchars($booking['name']); ?></h3>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?></p>
                            <p><strong>Jenis Tiket:</strong> <?php echo htmlspecialchars($booking['ticket_type']); ?></p>
                            <p><strong>Harga:</strong> Rp <?php echo number_format($booking['price'], 2, ',', '.'); ?></p>
                            <p><strong>Dipesan oleh:</strong> <?php echo htmlspecialchars($booking['booked_by']); ?></p>
                            <p><em>Tanggal Pemesanan:</em> <?php echo htmlspecialchars($booking['booking_date']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center;">Belum ada pemesanan tiket saat ini. Jadilah yang pertama memesan tiket
                        Anda!</p>
                <?php endif; ?>
            </div>
        </section>


        <section class="cta">
            <h2>Siap Memesan Tiket Anda?</h2>

            <p>Pilih jenis tiket dan pesan sekarang juga!</p>

            <a href="./views/register.php" class="cta-button">Mulai Sekarang</a>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; Copyright Ticketting 2024</p>
    </footer>
</body>

</html>