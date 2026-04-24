<?php
include 'auth.php';
include 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Alumni - Premium Track</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app-wrapper">
        <aside class="sidebar no-print">
            <div class="sidebar-header">
                <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
                <h1>Alumni<br>Tracking</h1>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php"><i class="fas fa-chart-pie"></i> Dashboard</a>
                <a href="input.php"><i class="fas fa-user-plus"></i> Input Data</a>
                <a href="list.php"><i class="fas fa-users"></i> Daftar Alumni</a>
                <a href="admin.php"><i class="fas fa-shield-alt"></i> Admin Panel</a>
                <a href="report.php" class="active"><i class="fas fa-file-alt"></i> Laporan Lulusan</a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header no-print">
                <div class="page-title">
                    <h2>Rekapitulasi Profil Lulusan</h2>
                    <p>Dokumen rekapitulasi komprehensif mencakup karir, kontak, dan rekam jejak alumni.</p>
                </div>
                
                <?php
                // Export URL Builder
                $export_params = [];
                if (isset($_GET['tahun']) && $_GET['tahun'] != '') $export_params[] = 'tahun=' . urlencode($_GET['tahun']);
                if (isset($_GET['status']) && $_GET['status'] != 'all') $export_params[] = 'status=' . urlencode($_GET['status']);
                $export_url = 'export_excel.php?' . implode('&', $export_params);
                ?>
                <div style="display:flex; gap:12px;">
                    <a href="<?php echo $export_url; ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Semua (Excel)</a>
                    <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Cetak Dokumen</button>
                </div>
            </div>

            <!-- Hanya muncul saat Print -->
            <div class="print-header" style="display:none; text-align:center; margin-bottom:30px;">
                <h1 style="font-size:1.5rem; margin-bottom:5px;">DOKUMEN INDUK DATA ALUMNI</h1>
                <p style="font-size:0.9rem; color:#666;">Dicetak pada: <?php echo date('d F Y H:i'); ?></p>
                <hr style="margin-top:15px; border:2px solid #000;">
            </div>

            <div class="card-panel no-print" style="padding: 20px; background:var(--bg-main);">
                <form action="report.php" method="GET" style="display:flex; gap:12px; align-items:center;">
                    <strong style="color:var(--text-muted);"><i class="fas fa-filter"></i> Saring :</strong>
                    <select name="tahun" class="form-control" style="width:auto; padding:8px 12px;">
                        <option value="">-- Semua Tahun Lulus --</option>
                        <?php
                        $opt_result = $conn->query("SELECT DISTINCT tahun_lulus FROM alumni ORDER BY tahun_lulus DESC");
                        $filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
                        if ($opt_result) {
                            while ($opt = $opt_result->fetch_assoc()) {
                                $selected = ($filter_tahun == $opt['tahun_lulus']) ? 'selected' : '';
                                echo "<option value='" . $opt['tahun_lulus'] . "' $selected>" . $opt['tahun_lulus'] . "</option>";
                            }
                        }
                        ?>
                    </select>

                    <select name="status" class="form-control" style="width:auto; padding:8px 12px;">
                        <?php $filter_status = isset($_GET['status']) ? $_GET['status'] : 'Verified'; ?>
                        <option value="Verified" <?php echo $filter_status == 'Verified' ? 'selected' : ''; ?>>Hanya Terverifikasi</option>
                        <option value="all" <?php echo $filter_status == 'all' ? 'selected' : ''; ?>>Semua Data Sistem</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm" style="padding:8px 16px;">Refresh</button>
                </form>
            </div>

            <?php
            // Query Builder
            $filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
            $filter_status = isset($_GET['status']) ? $_GET['status'] : 'Verified';

            $where_parts = [];
            if ($filter_status != 'all') { $where_parts[] = "status_verifikasi = '$filter_status'"; }
            if ($filter_tahun != '') { $where_parts[] = "tahun_lulus = '" . $conn->real_escape_string($filter_tahun) . "'"; }
            $where_sql = count($where_parts) > 0 ? "WHERE " . implode(" AND ", $where_parts) : "";

            $result = $conn->query("SELECT * FROM alumni $where_sql ORDER BY tahun_lulus DESC, nama ASC LIMIT 200"); // Limit for report preview
            $total_printed = $result ? $result->num_rows : 0;
            ?>

            <div class="table-responsive" style="box-shadow:none; border-radius:0;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th width="3%">No</th>
                            <th width="15%">Identitas (Nama & NIM)</th>
                            <th width="20%">Karir & Profesi (Posisi, Tipe, Instansi)</th>
                            <th width="20%">Alamat Tempat Kerja & Sosmed PT</th>
                            <th width="20%">Kontak Pribadi (HP & Email)</th>
                            <th width="22%">Sosial Media (IG, LinkedIn, dll)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if ($result && $total_printed > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                
                                // Identitas
                                echo "<td><strong style='color:#0f172a;'>" . htmlspecialchars($row['nama']) . "</strong><br><span style='font-size:0.8rem; color:#64748b;'>" . htmlspecialchars($row['nim']) . " - Lulus " . htmlspecialchars($row['tahun_lulus']) . "</span></td>";
                                
                                // Karir
                                echo "<td><strong style='color:#4338ca;'>" . htmlspecialchars($row['posisi'] ?: '-') . "</strong><br><span style='font-size:0.8rem;'>[" . htmlspecialchars($row['jenis_pekerjaan'] ?: '-') . "] - " . htmlspecialchars($row['tempat_bekerja'] ?: '') . "</span></td>";
                                
                                // Alamat & Sosmed PT
                                echo "<td><span style='font-size:0.85rem; color:#475569;'>" . htmlspecialchars($row['alamat_bekerja'] ?: '-') . "</span><br><span style='font-size:0.8rem; color:#3b82f6;'>@: " . htmlspecialchars($row['sosmed_tempat_kerja'] ?: '-') . "</span></td>";
                                
                                // Kontak
                                echo "<td><span style='font-size:0.85rem;'>" . htmlspecialchars($row['no_hp'] ?: '-') . "</span><br><span style='font-size:0.8rem; color:#64748b;'>" . htmlspecialchars($row['email'] ?: '-') . "</span></td>";
                                
                                // Sosmed Pribadi (Teks untuk print, biar terlihat isinya apa)
                                echo "<td style='font-size:0.8rem; color:#475569;'>
                                    " . (!empty($row['linkedin']) ? "IN: " . htmlspecialchars($row['linkedin']) . "<br>" : "") . "
                                    " . (!empty($row['ig']) ? "IG: " . htmlspecialchars($row['ig']) . "<br>" : "") . "
                                    " . (!empty($row['fb']) ? "FB: " . htmlspecialchars($row['fb']) . "<br>" : "") . "
                                    " . (!empty($row['tiktok']) ? "TK: " . htmlspecialchars($row['tiktok']) : "") . "
                                    " . (empty($row['linkedin']) && empty($row['ig']) && empty($row['fb']) && empty($row['tiktok']) ? "-" : "") . "
                                </td>";

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Data tidak ditemukan untuk kriteria ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top:20px; font-size:0.85rem; color:#64748b;" class="no-print">
                * Preview cetak dibatasi maksimal 200 baris pertama. File <strong>Download Excel</strong> akan men-download SEMUA (142.000+) baris dan ke-8 kategori data lengkap terpisah per kolom.
            </div>

            <div style="margin-top: 60px; display: flex; justify-content: flex-end; padding-right: 20px;">
                <div style="text-align: center; width: 260px;">
                    <p style="margin-bottom: 80px; font-size: 0.95rem; color: #000;">Mengetahui,<br><strong>Admin Pusat Data</strong></p>
                    <p style="font-weight: 600; color: #000; white-space: nowrap;">( ______________________________ )</p>
                    <p style="font-size: 0.85rem; color: #000; margin-top: 4px; text-align: left; padding-left: 15px;">NIP.</p>
                </div>
            </div>

        </main>
    </div>

    <style>
        @media print {
            .print-header { display: block !important; }
            .modern-table th { background: transparent !important; color:#000 !important; border-bottom: 2px solid #000 !important; }
            .modern-table td { color:#000 !important; border-bottom: 1px solid #ccc !important; }
        }
    </style>
</body>
</html>