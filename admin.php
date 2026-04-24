<?php
include 'auth.php';
include 'config.php';

$message = "";

// Proses verifikasi / hapus form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $action = $_POST['action'];

        if ($action == 'verify') {
            $stmt = $conn->prepare("UPDATE alumni SET status_verifikasi = 'Verified' WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "<div class='alert success'><i class='fas fa-check-circle'></i> Data alumni berhasil diverifikasi.</div>";
            }
            $stmt->close();
        } elseif ($action == 'reject') {
            $stmt = $conn->prepare("UPDATE alumni SET status_verifikasi = 'Rejected' WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "<div class='alert error'><i class='fas fa-ban'></i> Data alumni ditolak.</div>";
            }
            $stmt->close();
        } elseif ($action == 'delete') {
            $stmt = $conn->prepare("DELETE FROM alumni WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "<div class='alert success'><i class='fas fa-trash'></i> Data alumni berhasil dihapus.</div>";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Premium Review</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }
        .filter-group {
            display: flex;
            gap: 10px;
        }
        .filter-group input, .filter-group select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 8px 14px;
            font-family: 'Inter', sans-serif;
            background: #fff;
            outline: none;
            min-width: 180px;
        }
        .action-flex {
            display: flex;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
                <h1>Alumni<br>Tracking</h1>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php"><i class="fas fa-chart-pie"></i> Dashboard</a>
                <a href="input.php"><i class="fas fa-user-plus"></i> Input Data</a>
                <a href="list.php"><i class="fas fa-users"></i> Daftar Alumni</a>
                <a href="admin.php" class="active"><i class="fas fa-shield-alt"></i> Admin Panel</a>
                <a href="report.php"><i class="fas fa-file-alt"></i> Laporan Lulusan</a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header" style="margin-bottom: 20px;">
                <div class="page-title">
                    <h2>Admin Panel & Validasi Utama</h2>
                    <p>Modul review komprehensif untuk menyetujui, menolak, atau mengelola data alumni yang terdaftar.</p>
                </div>
                <div class="action-flex">
                    <a href="export_excel.php" class="btn btn-success"><i class="fas fa-file-excel"></i> Export CSV</a>
                    <a href="import_data.php" onclick="return confirm('Mulai proses import dari alumni_data.csv?');" class="btn btn-primary" style="background:#6366f1;"><i class="fas fa-cloud-upload-alt"></i> Import Batch</a>
                </div>
            </div>

            <?php echo $message; ?>

            <div class="card-panel" style="padding: 24px;">
                <form action="admin.php" method="GET" class="admin-controls">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Pencarian Nama/NIM..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <select name="filter_status">
                            <?php $filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : 'all'; ?>
                            <option value="all" <?php echo $filter_status == 'all' ? 'selected' : ''; ?>>Semua Status</option>
                            <option value="Pending" <?php echo $filter_status == 'Pending' ? 'selected' : ''; ?>>Hanya Pending</option>
                            <option value="Verified" <?php echo $filter_status == 'Verified' ? 'selected' : ''; ?>>Hanya Verified</option>
                            <option value="Rejected" <?php echo $filter_status == 'Rejected' ? 'selected' : ''; ?>>Hanya Rejected</option>
                        </select>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Analisa Data</button>
                        <?php if ((isset($_GET['search']) && $_GET['search'] != '') || (isset($_GET['filter_status']) && $_GET['filter_status'] != 'all')): ?>
                            <a href="admin.php" class="btn btn-secondary">Reset Filter</a>
                        <?php endif; ?>
                    </div>
                </form>

                <?php
                $batas = 50;
                $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
                if ($halaman < 1) $halaman = 1;

                $search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
                $filter_status = isset($_GET['filter_status']) ? $conn->real_escape_string($_GET['filter_status']) : 'all';

                $where_parts = [];
                if ($search != '') { $where_parts[] = "(nama LIKE '%$search%' OR nim LIKE '%$search%')"; }
                if ($filter_status != 'all') { $where_parts[] = "status_verifikasi = '$filter_status'"; }
                $where_sql = count($where_parts) > 0 ? "WHERE " . implode(" AND ", $where_parts) : "";

                $query_total = $conn->query("SELECT COUNT(*) as total FROM alumni $where_sql");
                $jumlah_data = $query_total ? $query_total->fetch_assoc()['total'] : 0;
                $total_halaman = max(1, ceil($jumlah_data / $batas));
                if ($halaman > $total_halaman) $halaman = $total_halaman;
                $halaman_awal = ($halaman - 1) * $batas;

                $result = $conn->query("SELECT * FROM alumni $where_sql ORDER BY FIELD(status_verifikasi,'Pending','Rejected','Verified'), created_at DESC LIMIT $halaman_awal, $batas");

                $param_str = '';
                if ($search) $param_str .= '&search='.urlencode($search);
                if ($filter_status != 'all') $param_str .= '&filter_status='.urlencode($filter_status);
                ?>

                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="3%">#</th>
                                <th width="20%">Akademik</th>
                                <th width="25%">Profesional & Sosmed</th>
                                <th width="20%">Kontak</th>
                                <th width="12%">Status</th>
                                <th width="20%">Validasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = $halaman_awal + 1;
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $statusClass = strtolower($row['status_verifikasi'] ?? 'pending');
                                    $jenisClass = strtolower($row['jenis_pekerjaan'] ?? '');
                                    
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    
                                    // Col: Akademik
                                    echo "<td>
                                        <div class='user-meta'>
                                            <div class='user-name'>" . htmlspecialchars($row['nama']) . "</div>
                                            <div class='user-nim' style='font-family:monospace;'>NIM. " . htmlspecialchars($row['nim']) . "</div>
                                            <div style='font-size:0.8rem; color:#6366f1; font-weight:500;'>" . htmlspecialchars($row['program_studi']) . "</div>
                                        </div>
                                    </td>";

                                    // Col: Profesional & Sosmed (Points 1,4,5,6,7,8 combined smartly)
                                    echo "<td>";
                                    if(!empty($row['pekerjaan']) || !empty($row['tempat_bekerja'])){
                                        echo "<div style='font-size:0.85rem; font-weight:600; color:var(--text-main);'>" . ($row['jenis_pekerjaan'] ? "<span class='badge {$jenisClass}'>{$row['jenis_pekerjaan']}</span> " : "") . htmlspecialchars($row['posisi'] ?? $row['pekerjaan']) . "</div>";
                                        echo "<div style='font-size:0.8rem; color:#475569; margin-top:2px;'><i class='fas fa-building'></i> " . htmlspecialchars($row['tempat_bekerja'] ?? '-') . "</div>";
                                    } else {
                                        echo "<span style='color:#94a3b8; font-size:0.8rem;'>- Data Profesi Kosong -</span>";
                                    }
                                    
                                    // Social Icons logic
                                    echo "<div class='social-icons' style='margin-top:8px;'>";
                                        if(!empty($row['linkedin'])) echo "<a href='#' class='linkedin'><i class='fab fa-linkedin'></i></a>";
                                        if(!empty($row['ig'])) echo "<a href='#' class='ig'><i class='fab fa-instagram'></i></a>";
                                        if(!empty($row['fb'])) echo "<a href='#' class='fb'><i class='fab fa-facebook'></i></a>";
                                        if(!empty($row['tiktok'])) echo "<a href='#' class='tiktok'><i class='fab fa-tiktok'></i></a>";
                                    echo "</div>";
                                    echo "</td>";

                                    // Col: Kontak (Points 2, 3)
                                    echo "<td>
                                        <div class='user-meta'>
                                            " . (empty($row['email']) ? "-" : "<div class='contact-pill'><i class='fas fa-at'></i> " . htmlspecialchars($row['email']). "</div>") . "
                                            " . (empty($row['no_hp']) ? "-" : "<div class='contact-pill'><i class='fas fa-phone-alt'></i> " . htmlspecialchars($row['no_hp']). "</div>") . "
                                        </div>
                                    </td>";

                                    // Col: Status
                                    echo "<td><span class='badge {$statusClass}'>" . htmlspecialchars($row['status_verifikasi']) . "</span></td>";

                                    // Col: Validasi (Action Buttons)
                                    echo "<td><div style='display:flex; flex-direction:column; gap:6px;'>";
                                    
                                    if ($row['status_verifikasi'] != 'Verified') {
                                        echo "<form action='admin.php' method='POST' style='margin:0;'>
                                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                                            <input type='hidden' name='action' value='verify'>
                                            <button type='submit' class='btn btn-success btn-sm' style='width:100%;'><i class='fas fa-check'></i> Verifikasi</button>
                                        </form>";
                                    }
                                    if ($row['status_verifikasi'] != 'Rejected') {
                                        echo "<form action='admin.php' method='POST' style='margin:0;'>
                                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                                            <input type='hidden' name='action' value='reject'>
                                            <button type='submit' class='btn btn-warning btn-sm' style='width:100%; color:#fff;'><i class='fas fa-times'></i> Tolak</button>
                                        </form>";
                                    }
                                    echo "<form action='admin.php' method='POST' style='margin:0;' onsubmit='return confirm(\"Hapus permanen?\");'>
                                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                                        <input type='hidden' name='action' value='delete'>
                                        <button type='submit' class='btn btn-danger btn-sm' style='width:100%;'><i class='fas fa-trash'></i> Hapus</button>
                                    </form>";
                                    
                                    echo "</div></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center' style='padding:40px; color:#94a3b8;'>Belum ada data untuk diproses.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <?php if($total_halaman > 1): ?>
                <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <p style="font-size: 0.9rem; color: #64748b;">Halaman <?php echo $halaman; ?> / <?php echo $total_halaman; ?></p>
                    <div style="display: flex; gap: 8px;">
                        <?php if ($halaman > 1): ?>
                            <a href="?halaman=<?php echo $halaman - 1 . $param_str; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Prev</a>
                        <?php endif; ?>
                        <?php if ($halaman < $total_halaman): ?>
                            <a href="?halaman=<?php echo $halaman + 1 . $param_str; ?>" class="btn btn-secondary btn-sm">Next <i class="fas fa-arrow-right"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
        </main>
    </div>
</body>
</html>