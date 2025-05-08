<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'] ?? 'Pengguna';

$conn = new mysqli("localhost", "root", "", "orange_labo", 8111);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax'])) {
    $response = ['success' => false, 'message' => ''];
    
    if (isset($_POST['nama'], $_POST['email'], $_POST['komentar'])) {
        $nama = htmlspecialchars(trim($_POST['nama']));
        $email = htmlspecialchars(trim($_POST['email']));
        $komentar = htmlspecialchars(trim($_POST['komentar']));
        $waktu = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO guestbook (waktu, nama, email, komentar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $waktu, $nama, $email, $komentar);
        
        if ($stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'Pesan berhasil dikirim',
                'entry' => [
                    'nama' => $nama,
                    'email' => $email,
                    'komentar' => $komentar,
                    'waktu' => $waktu
                ]
            ];
        } else {
            $response['message'] = 'Gagal mengirim pesan: ' . $stmt->error;
        }
    } else {
        $response['message'] = 'Data tidak lengkap';
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nama'], $_POST['email'], $_POST['komentar'])) {
    $nama = htmlspecialchars(trim($_POST['nama']));
    $email = htmlspecialchars(trim($_POST['email']));
    $komentar = htmlspecialchars(trim($_POST['komentar']));
    $waktu = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO guestbook (waktu, nama, email, komentar) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $waktu, $nama, $email, $komentar);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'loadEntries') {
    $entries = [];
    $result = $conn->query("SELECT waktu, nama, email, komentar FROM guestbook ORDER BY waktu DESC");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $entries[] = $row;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'entries' => $entries]);
    exit();
}

$entries = [];
$result = $conn->query("SELECT waktu, nama, email, komentar FROM guestbook ORDER BY waktu DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orange Labo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'Lazydog';
            src: url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/webfonts/lazy-dog.woff2') format('woff2');
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #FFF1E6;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            min-height: 100vh;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            width: 100%;
        }

        .title {
            font-size: 60px;
            font-family: 'Lazydog', cursive;
            margin-bottom: 5px;
            line-height: 1;
        }

        .subtitle {
            font-size: 24px;
            font-weight: bold;
        }

        .main-container {
            width: 100%;
            max-width: 1000px;
            background-color: #FFF;
            border-radius: 15px;
            overflow: visible;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            margin-bottom: 40px;
        }

        .welcome-header {
            background-color: #FF6B00;
            color: #FFF;
            padding: 20px;
            text-align: center;
        }

        .welcome-header h1 {
            font-size: 26px;
            font-weight: bold;
        }

        .content-section {
            padding: 30px;
        }

        .section-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background-color: #FFF;
            color: #333;
            margin-bottom: 15px;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            background-color: #FF6B00;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #e05a00;
        }

        .logout-btn {
            background-color: #FFF;
            color: #FF6B00;
            border: 2px solid #FF6B00;
        }

        .logout-btn:hover {
            background-color: #FF6B00;
            color: #FFF;
        }

        .entries {
            margin-top: 40px;
        }

        .entry {
            background-color: #FFF;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }

        .entry h3 {
            color: #333;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .entry p {
            color: #555;
            margin-bottom: 10px;
        }

        .entry small {
            color: #888;
            font-size: 14px;
            display: block;
            margin-top: 5px;
        }

        .no-entries {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 20px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .logo-circle {
            width: 80px;
            height: 80px;
            background-color: #FF6B00;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
        }

        .star-icon {
            width: 40px;
            height: 40px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 20px;
            color: #333;
            font-weight: 400;
        }

        .message-form {
            background-color: #f8f8f8;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .mascot {
            position: absolute;
            right: -90px;
            bottom: -90px;
            width: 170px;
            height: auto;
            z-index: 10;
        }

        .mascot-small {
            position: absolute;
            left: -50px;
            top: -60px;
            width: 150px;
            height: auto;
            z-index: 10;
        }

        /* Notification styling */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background-color: #4CAF50;
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s, transform 0.3s;
        }

        .notification.error {
            background-color: #F44336;
        }

        /* Loading indicator */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .refresh-btn {
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
            display: inline-flex;
            align-items: center;
        }

        .refresh-btn:hover {
            background-color: #e0e0e0;
        }

        .refresh-btn svg {
            margin-right: 5px;
        }

        .entries-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .title {
                font-size: 40px;
            }
            .subtitle {
                font-size: 18px;
            }
            .main-container {
                padding: 20px;
            }
            .mascot {
                right: 20px;
                bottom: -60px;
                width: 120px;
            }
            .mascot-small {
                left: 20px;
                top: -30px;
                width: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">ORANGE LABO</h1>
        <p class="subtitle">Co - Working Space</p>
    </div>

    <div class="main-container">
        <img class="mascot-small" src="Mascot Pemweb3.PNG" alt="Orange Labo mascot character">
        
        <div class="welcome-header">
            <h1>Selamat Datang, <?php echo htmlspecialchars($username); ?>!</h1>
        </div>
        
        <div class="content-section">
            <div class="logo-circle">
                <svg class="star-icon" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2">
                    <path d="M12 1.5l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.94l-6.18 3.08L7 14.14 2 9.27l6.91-1.01L12 1.5z"></path>
                </svg>
            </div>
            
            <div class="message-form">
                <h2 class="section-title">Tulis Pesan</h2>
                <form id="messageForm" method="POST">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" name="nama" placeholder="Nama Anda" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Email Anda" required>
                    </div>
                    <div class="form-group">
                        <label for="komentar">Pesan</label>
                        <textarea id="komentar" name="komentar" placeholder="Tulis pesan Anda..." required></textarea>
                    </div>
                    <div style="text-align: right;">
                        <button type="submit" class="btn" id="submitBtn">
                            Kirim Pesan
                            <svg style="width: 20px; height: 14px; margin-left: 5px; display: inline-block; vertical-align: middle;" viewBox="0 0 24 12" fill="none" stroke="white" stroke-width="2">
                                <line x1="1" y1="6" x2="18" y2="6"></line>
                                <polyline points="13,1 18,6 13,11"></polyline>
                            </svg>
                            <span class="loading-spinner" style="display: none;"></span>
                        </button>
                    </div>
                    <!-- Hidden field to indicate AJAX request -->
                    <input type="hidden" name="ajax" value="1">
                </form>
            </div>
            
            <div class="entries">
                <div class="entries-header">
                    <h2 class="section-title">Daftar Pesan</h2>
                    <button class="refresh-btn" id="refreshEntries">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M23 4v6h-6"></path>
                            <path d="M1 20v-6h6"></path>
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10"></path>
                            <path d="M20.49 15a9 9 0 0 1-14.85 3.36L1 14"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
                <div id="entriesContainer">
                    <?php if (!empty($entries)): ?>
                        <?php foreach ($entries as $entry): ?>
                            <div class="entry">
                                <h3><?php echo htmlspecialchars($entry['nama']); ?></h3>
                                <p><?php echo htmlspecialchars($entry['komentar']); ?></p>
                                <small>Email: <?php echo htmlspecialchars($entry['email']); ?></small>
                                <small><?php echo $entry['waktu']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-entries">Belum ada pesan.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="button-container">
                <a href="logout.php" class="btn logout-btn">Logout</a>
            </div>
        </div>
        
        <img class="mascot" src="Mascot Pemweb2.PNG" alt="Orange Labo mascot character">
    </div>

    <div class="footer">
        <p>"Do your task with us"</p>
    </div>

    <script src="ajax.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageForm = document.getElementById('messageForm');
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = submitBtn.querySelector('.loading-spinner');
            const entriesContainer = document.getElementById('entriesContainer');
            const refreshBtn = document.getElementById('refreshEntries');

            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                submitBtn.disabled = true;
                loadingSpinner.style.display = 'inline-block';
                
                const formData = new FormData(messageForm);
                
                ajaxFormSubmit('dashboard.php', formData, 
                    function(response) {
                        if (response.success) {
                            messageForm.reset();
                            
                            showNotification(response.message, 'success');
                            
                            addNewEntry(response.entry);
                        } else {
                            showNotification(response.message || 'Terjadi kesalahan', 'error');
                        }
                        
                        submitBtn.disabled = false;
                        loadingSpinner.style.display = 'none';
                    },
                    function(status, statusText) {
                        showNotification('Terjadi kesalahan: ' + status + ' ' + statusText, 'error');
                        
                        submitBtn.disabled = false;
                        loadingSpinner.style.display = 'none';
                    }
                );
            });
            
            refreshBtn.addEventListener('click', function() {
                loadEntries();
            });
            
            function loadEntries() {
                entriesContainer.innerHTML = '<p class="no-entries">Loading...</p>';
                
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'dashboard.php?action=loadEntries', true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success && response.entries) {
                                    updateEntriesDisplay(response.entries);
                                } else {
                                    entriesContainer.innerHTML = '<p class="no-entries">Gagal memuat pesan.</p>';
                                }
                            } catch (e) {
                                entriesContainer.innerHTML = '<p class="no-entries">Error parsing response.</p>';
                            }
                        } else {
                            entriesContainer.innerHTML = '<p class="no-entries">Error: ' + xhr.status + '</p>';
                        }
                    }
                };
                xhr.send();
            }
            
            function updateEntriesDisplay(entries) {
                if (entries.length === 0) {
                    entriesContainer.innerHTML = '<p class="no-entries">Belum ada pesan.</p>';
                    return;
                }
                
                entriesContainer.innerHTML = '';
                entries.forEach(function(entry) {
                    addNewEntry(entry, true);
                });
            }
            
            function addNewEntry(entry, append = false) {
                const entryElement = document.createElement('div');
                entryElement.className = 'entry';
                entryElement.innerHTML = `
                    <h3>${escapeHTML(entry.nama)}</h3>
                    <p>${escapeHTML(entry.komentar)}</p>
                    <small>Email: ${escapeHTML(entry.email)}</small>
                    <small>${entry.waktu}</small>
                `;
    
                const noEntries = entriesContainer.querySelector('.no-entries');
                if (noEntries) {
                    entriesContainer.removeChild(noEntries);
                }
                
                if (append) {
                    entriesContainer.appendChild(entryElement);
                } else {
                    entriesContainer.insertBefore(entryElement, entriesContainer.firstChild);
                }
                
                setTimeout(function() {
                    entryElement.style.backgroundColor = '#FFEB3B';
                    setTimeout(function() {
                        entryElement.style.transition = 'background-color 1s ease';
                        entryElement.style.backgroundColor = '#FFF';
                    }, 100);
                }, 0);
            }
            
            function escapeHTML(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }
        });
    </script>
</body>
</html>