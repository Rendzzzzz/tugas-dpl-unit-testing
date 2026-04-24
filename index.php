<?php
include 'auth.php';
include 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pelacakan Alumni</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
                <h1>Alumni<br>Tracking</h1>
            </div>
            
            <nav class="sidebar-nav">
                <a href="index.php" class="active"><i class="fas fa-chart-pie"></i> Dashboard</a>
                <a href="input.php"><i class="fas fa-user-plus"></i> Input Data</a>
                <a href="list.php"><i class="fas fa-users"></i> Daftar Alumni</a>
                <a href="admin.php"><i class="fas fa-shield-alt"></i> Admin Panel</a>
                <a href="report.php"><i class="fas fa-file-alt"></i> Laporan Lulusan</a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <div class="page-title">
                    <h2>Dashboard Utama</h2>
                    <p>Selamat Datang, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></strong>. Berikut adalah ringkasan data alumni hari ini.</p>
                </div>
            </div>

            <?php
            // Fetch comprehensive stats
            $stats = [];
            $q = $conn->query("SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status_verifikasi = 'Verified' THEN 1 ELSE 0 END) as verified,
                SUM(CASE WHEN status_verifikasi = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status_verifikasi = 'Rejected' THEN 1 ELSE 0 END) as rejected
                FROM alumni");
            $stats = $q ? $q->fetch_assoc() : ['total' => 0, 'verified' => 0, 'pending' => 0, 'rejected' => 0];
            ?>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                    <div class="stat-details">
                        <h3>Total Alumni</h3>
                        <div class="num"><?php echo number_format($stats['total'] ?? 0, 0, ',', '.'); ?></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-details">
                        <h3>Terverifikasi</h3>
                        <div class="num"><?php echo number_format($stats['verified'] ?? 0, 0, ',', '.'); ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
                    <div class="stat-details">
                        <h3>Menunggu Verifikasi</h3>
                        <div class="num"><?php echo number_format($stats['pending'] ?? 0, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>

            <div class="card-panel">
                <h3 class="section-title"><i class="fas fa-rocket"></i> Akses Cepat</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 16px;">
                    <a href="input.php" style="padding: 24px; background: #eff6ff; border-radius: 12px; text-decoration: none; color: #1e40af; border: 1px solid #bfdbfe; transition: all 0.2s;">
                        <i class="fas fa-user-plus" style="font-size: 24px; margin-bottom: 12px; display: block;"></i>
                        <strong>Tambah Alumni Baru</strong>
                        <p style="font-size: 0.85rem; color: #64748b; margin-top: 4px;">Form registrasi manual.</p>
                    </a>
                    
                    <a href="admin.php" style="padding: 24px; background: #fefce8; border-radius: 12px; text-decoration: none; color: #92400e; border: 1px solid #fde68a; transition: all 0.2s;">
                        <i class="fas fa-user-check" style="font-size: 24px; margin-bottom: 12px; display: block;"></i>
                        <strong>Proses Verifikasi</strong>
                        <p style="font-size: 0.85rem; color: #64748b; margin-top: 4px;">Tinjau alumni pending.</p>
                    </a>
                    
                    <a href="import_data.php" style="padding: 24px; background: #fdf4ff; border-radius: 12px; text-decoration: none; color: #86198f; border: 1px solid #f5d0fe; transition: all 0.2s;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 24px; margin-bottom: 12px; display: block;"></i>
                        <strong>Import Database Bulk</strong>
                        <p style="font-size: 0.85rem; color: #64748b; margin-top: 4px;">Import ribuan data CSV.</p>
                    </a>
                </div>
            </div>
            
        </main>
    </div>
</body>
</html>