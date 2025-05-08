<?php
// Check if AJAX request
if (isset($_GET['ajax'])) {
    session_start();
    session_unset();
    session_destroy();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Logout berhasil.']);
    exit();
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    session_start();
    session_unset();
    session_destroy();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Logout berhasil.']);
    exit();
} else {
    // Regular non-AJAX logout
    session_start();
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>