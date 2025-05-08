<?php
session_start();

$conn = new mysqli("localhost", "root", "", "orange_labo", 8111);
if ($conn->connect_error) {
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Koneksi database gagal.']);
        exit();
    } else {
        die("Koneksi gagal: " . $conn->connect_error);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $isAjax = isset($_POST['ajax']);

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($stored_password);
        $stmt->fetch();

        if ($password === $stored_password) {
            $_SESSION['login'] = true;
            $_SESSION['username'] = $username;
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Login berhasil.']);
            } else {
                header("Location: dashboard.php");
            }
            exit();
        }
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Username atau password salah']);
    } else {
        header("Location: login.php?error=Username atau password salah");
    }
    exit();
} else {
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
    } else {
        header("Location: login.php");
    }
    exit();
}
?>