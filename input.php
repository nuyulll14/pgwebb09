<?php
// Ambil data dari formulir dan sanitasi input
$kecamatan = isset($_POST['kecamatan']) ? trim($_POST['kecamatan']) : '';
$luas = isset($_POST['luas']) ? (float)$_POST['luas'] : 0; // Pastikan luas adalah angka
$jumlah_penduduk = isset($_POST['jumlah_penduduk']) ? (int)$_POST['jumlah_penduduk'] : 0; // Pastikan jumlah_penduduk adalah integer

// Sesuaikan dengan setting MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pgwebacara8_neww";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pastikan semua data diisi
if (!empty($kecamatan) && $luas > 0 && $jumlah_penduduk > 0) {
    // Menggunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("INSERT INTO penduduk (kecamatan, luas, jumlah_penduduk) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $kecamatan, $luas, $jumlah_penduduk); // s = string, i = integer

    // Eksekusi dan periksa hasil
    if ($stmt->execute()) {
        echo "New record created successfully";
        // Redirect after successful insert (uncomment if needed)
        // header("Location: index.html");
        // exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close(); // Tutup statement setelah digunakan
} else {
    echo "Semua field harus diisi dengan benar.";
}

$conn->close(); // Tutup koneksi
?>
