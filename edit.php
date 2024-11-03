<?php
// Konfigurasi koneksi MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pgwebacara8_neww";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Inisialisasi variabel
$id = $kecamatan = $latitude = $longitude = $luas = $jumlah_penduduk = "";

// Jika ada ID dalam URL, ambil data dari database
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM penduduk WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $kecamatan = $row['kecamatan'];
        $latitude = $row['latitude'];
        $longitude = $row['longitude'];
        $luas = $row['luas'];
        $jumlah_penduduk = $row['jumlah_penduduk'];
    }
}

// Mengupdate data jika formulir disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kecamatan = $_POST['kecamatan'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $luas = $_POST['luas'];
    $jumlah_penduduk = $_POST['jumlah_penduduk'];

    $update_sql = "UPDATE penduduk SET kecamatan=?, latitude=?, longitude=?, luas=?, jumlah_penduduk=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssddii", $kecamatan, $latitude, $longitude, $luas, $jumlah_penduduk, $id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Data updated successfully'); window.location.href='index.php';</script>";
    } else {
        echo "Error updating record: " . $update_stmt->error;
    }
    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            max-width: 400px;
        }
        label {
            margin-bottom: 5px;
        }
        input {
            margin-bottom: 15px;
            padding: 8px;
            font-size: 1em;
        }
        button {
            padding: 10px;
            background-color: cyan;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Edit Data Kecamatan</h2>
    <form method="POST" action="">
        <label for="kecamatan">Kecamatan:</label>
        <input type="text" name="kecamatan" value="<?php echo htmlspecialchars($kecamatan); ?>" required>

        <label for="latitude">Latitude:</label>
        <input type="text" name="latitude" value="<?php echo htmlspecialchars($latitude); ?>" required>

        <label for="longitude">Longitude:</label>
        <input type="text" name="longitude" value="<?php echo htmlspecialchars($longitude); ?>" required>

        <label for="luas">Luas:</label>
        <input type="number" name="luas" value="<?php echo htmlspecialchars($luas); ?>" required>

        <label for="jumlah_penduduk">Jumlah Penduduk:</label>
        <input type="number" name="jumlah_penduduk" value="<?php echo htmlspecialchars($jumlah_penduduk); ?>" required>

        <button type="submit">Update</button>
    </form>
</body>
</html>