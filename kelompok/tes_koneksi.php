<?php
$conn = new mysqli("localhost", "root", "", "orange_labo", 8111);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
echo "Koneksi ke database BERHASIL!";
?>
