<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Web GIS Kabupaten Sleman</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            font-size: 24px;
            padding: 20px;
            background-color: #f0f0f0;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .container {
            display: flex;
            width: 90%;
            justify-content: space-between;
            margin-top: 20px;
        }

        /* Sidebar untuk tabel data */
        #data-table {
            width: 30%; /* Set width of the data table to 30% */
            padding: 10px;
            background-color: #ffffff; /* White background for the table container */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Pengaturan untuk tabel */
        table {
            width: 100%; /* Set table width to 100% of its container */
            border-collapse: collapse;
            font-size: 0.9em;
            margin: 0 auto; /* Center the table */
            background-color: #ffffff; /* White background for the table */
        }

        th, td {
            border: 1px solid #ddd;
            padding: 4px; /* Adjusted padding for cells */
            text-align: left;
            overflow: hidden; /* Hide overflow for better fit */
            white-space: nowrap; /* Prevent text wrapping */
            text-overflow: ellipsis; /* Add ellipsis for overflow text */
        }

        th {
            background-color: #f2f2f2; /* Light gray background for headers */
            font-weight: bold;
        }

        /* Tombol Hapus */
        button {
            background-color: red;
            color: white;
            border: none;
            padding: 4px 8px;
            cursor: pointer;
            font-size: 0.8em;
        }

        /* Tombol Edit */
        .edit-button {
            background-color: cyan;
            color: black;
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
        }

        /* Container untuk peta */
        #map-container {
            width: 40%;
            height: 70vh;
            position: relative;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        /* Warna teks pada angka di tabel */
        td {
            color: black;
        }

        /* Warna hijau untuk judul tabel */
        .table-title {
            color: green; /* Warna hijau */
            margin-bottom: 10px; /* Jarak antara judul dan tabel */
        }

        .map-title {
            color: green; /* Warna hijau */
            margin-bottom: 10px; /* Jarak antara judul dan peta */
        }
    </style>
</head>

<body>
    <h1>Web GIS Kabupaten Sleman</h1>

    <div class="container">
        <div id="data-table">
            <h3 class="table-title">Tabel Kecamatan</h3>
            <?php
            // Konfigurasi koneksi MySQL
            $servername = "localhost";
            $username = "root";
            $password = ""; // Sesuaikan jika ada password untuk root
            $dbname = "pgwebacara8_neww";

            // Membuat koneksi
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Memeriksa koneksi
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Aksi Delete Data
            if (isset($_POST['delete_id'])) {
                $delete_id = intval($_POST['delete_id']); // Pastikan id adalah integer
                // Menggunakan prepared statement untuk keamanan
                $stmt = $conn->prepare("DELETE FROM penduduk WHERE id = ?");
                $stmt->bind_param("i", $delete_id);

                if ($stmt->execute()) {
                    echo "<p>Record deleted successfully</p>";
                } else {
                    echo "<p>Error deleting record: " . $stmt->error . "</p>";
                }
                $stmt->close();
            }

            // Mengambil data dari tabel `penduduk`
            $sql = "SELECT id, kecamatan, latitude, longitude, luas, jumlah_penduduk FROM penduduk";
            $result = $conn->query($sql);

            // Menginisialisasi array JavaScript untuk menyimpan data marker
            echo "<script>var locations = [];</script>";

            if ($result->num_rows > 0) {
                echo "<form method='POST' action=''>";
                echo "<table class='table'>
                    <thead>
                        <tr>
                            <th>Kecamatan</th>
                            <th>Longitude</th>
                            <th>Latitude</th>
                            <th>Luas</th>
                            <th>Jumlah Penduduk</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row["kecamatan"]) . "</td>
                        <td>" . htmlspecialchars($row["longitude"]) . "</td>
                        <td>" . htmlspecialchars($row["latitude"]) . "</td>
                        <td>" . htmlspecialchars($row["luas"]) . "</td>
                        <td>" . htmlspecialchars($row["jumlah_penduduk"]) . "</td>
                        <td>
                            <button type='submit' name='delete_id' value='" . htmlspecialchars($row["id"]) . "' onclick=\"return confirm('Apakah Anda yakin ingin menghapus data ini?');\">Hapus</button>
                            <a href='edit.php?id=" . htmlspecialchars($row["id"]) . "' class='edit-button'>Edit</a>
                        </td>
                    </tr>";

                    // Menambahkan data marker ke array JavaScript
                    echo "<script>locations.push([" . json_encode($row['latitude']) . ", " . json_encode($row['longitude']) . ", " . json_encode($row['kecamatan']) . ", " . json_encode($row['luas']) . ", " . json_encode($row['jumlah_penduduk']) . "]);</script>";
                }

                echo "</tbody></table>";
                echo "</form>";
            } else {
                echo "<p>0 results</p>";
            }

            // Menutup koneksi
            $conn->close();
            ?>
        </div>

        <div id="map-container">
            <h3 class="map-title">Peta Kecamatan</h3>
            <div id="map"></div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        var map = L.map("map").setView([-7.7169, 110.355], 11);

        var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        // Menambahkan markers dari PHP ke peta dengan informasi lengkap
        locations.forEach(function(location) {
            L.marker([location[0], location[1]])
                .addTo(map)
                .bindPopup("<strong>Kecamatan:</strong> " + location[2] +
                    "<br><strong>Luas:</strong> " + location[3] + " ha" +
                    "<br><strong>Longitude:</strong> " + location[1] +
                    "<br><strong>Latitude:</strong> " + location[0] +
                    "<br><strong>Jumlah Penduduk:</strong> " + location[4]);
        });
    </script>
</body>

</html>
