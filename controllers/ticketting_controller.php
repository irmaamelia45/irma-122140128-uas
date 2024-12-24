<?php
include '../config/db_config.php';

header('Content-Type: application/json');

// Ambil parameter 'action'
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Ambil semua data pemesanan tiket
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetch') {
    $query = "SELECT * FROM ticket_bookings ORDER BY booking_date DESC";
    $result = $conn->query($query);
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    echo json_encode($bookings);
    exit;
}

// Ambil pemesanan tiket berdasarkan ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetchById') {
    $id = htmlspecialchars($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM ticket_bookings WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Pemesanan tiket tidak ditemukan."]);
    }
    $stmt->close();
    exit;
}

// Ambil semua data pemesanan tiket
// if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetch') {
//     $query = "SELECT ticket_bookings.id, ticket_bookings.name, ticket_bookings.email, ticket_bookings.ticket_type, ticket_bookings.price, ticket_bookings.booking_date, users.name AS booked_by 
//               FROM ticket_bookings 
//               INNER JOIN users ON ticket_bookings.user_id = users.id 
//               ORDER BY ticket_bookings.booking_date DESC";
//     $result = $conn->query($query);
//     $bookings = [];
//     while ($row = $result->fetch_assoc()) {
//         $bookings[] = $row;
//     }
//     echo json_encode($bookings);
//     exit;
// }

// // Ambil pemesanan tiket berdasarkan ID
// if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'fetchById') {
//     $id = htmlspecialchars($_GET['id']);
//     $stmt = $conn->prepare("SELECT ticket_bookings.id, ticket_bookings.name, ticket_bookings.email, ticket_bookings.ticket_type, ticket_bookings.price, ticket_bookings.booking_date, users.name AS booked_by 
//                              FROM ticket_bookings 
//                              INNER JOIN users ON ticket_bookings.user_id = users.id 
//                              WHERE ticket_bookings.id = ?");
//     $stmt->bind_param("s", $id);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     if ($result->num_rows > 0) {
//         echo json_encode($result->fetch_assoc());
//     } else {
//         echo json_encode(["error" => "Pemesanan tidak ditemukan."]);
//     }
//     $stmt->close();
//     exit;
// }

// Tambahkan pemesanan tiket baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $id = generateUuid(); // Hasilkan UUID
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $ticket_type = htmlspecialchars($_POST['ticket_type']);
    $price = htmlspecialchars($_POST['price']);
    $user_id = htmlspecialchars($_POST['user_id']);

    $stmt = $conn->prepare("INSERT INTO ticket_bookings (id, user_id, name, email, ticket_type, price, booking_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssd", $id, $user_id, $name, $email, $ticket_type, $price);

    if ($stmt->execute()) {
        http_response_code(200); // Tambahkan ini untuk respon sukses
        echo json_encode(["message" => "Pemesanan tiket berhasil ditambahkan!", "id" => $id]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Gagal menambahkan pemesanan tiket."]);
    }
    $stmt->close();
    exit;
}

// Perbarui data pemesanan tiket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    $id = htmlspecialchars($_POST['id']);
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $ticket_type = htmlspecialchars($_POST['ticket_type']);
    $price = htmlspecialchars($_POST['price']);

    $stmt = $conn->prepare("UPDATE ticket_bookings SET name = ?, email = ?, ticket_type = ?, price = ? WHERE id = ?");
    $stmt->bind_param("sssds", $name, $email, $ticket_type, $price, $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Pemesanan tiket berhasil diperbarui!"]);
    } else {
        echo json_encode(["error" => "Gagal memperbarui pemesanan tiket."]);
    }
    $stmt->close();
    exit;
}

// Hapus pemesanan tiket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    $id = htmlspecialchars($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM ticket_bookings WHERE id = ?");
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Pemesanan tiket berhasil dihapus!"]);
    } else {
        echo json_encode(["error" => "Gagal menghapus pemesanan tiket."]);
    }
    $stmt->close();
    exit;
}

// Jika 'action' tidak valid
http_response_code(400);
echo json_encode(["error" => "Parameter 'action' tidak valid."]);
$conn->close();

// Fungsi untuk menghasilkan UUID
function generateUuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
