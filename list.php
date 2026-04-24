<?php
include 'auth.php';
include 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Alumni - Premium Track</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .filter-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            padding: 16px;
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-bar input, .filter-bar select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            outline: none;
            transition: all 0.2s;
            min-width: 200px;
        }
        .filter-bar input:focus, .filter-bar select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
            background: #fff;
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
                <a href="list.php" class="active"><i class="fas fa-users"></i> Daftar Alumni</a>
                <a href="admin.php"><i class="fas fa-shield-alt"></i> Admin Panel</a>
                <a href="report.php"><i class="fas fa-file-alt"></i> Laporan Lulusan</a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <div class="page-title">
                    <h2>Direktori Semua Alumni</h2>
                    <p>Melihat dan melacak jejak rekam, pekerjaan, serta media sosial lulusan.</p>
                </div>
            </div>

            <!-- Form Pencarian -->
            <form action="list.php" method="GET" class="filter-bar">
                <i class="fas fa-search" style="color:#94a3b8; font-size:1.1rem; margin-right:-4px;"></i>
                <input type="text" name="search" placeholder="Cari Nama, NIM, Institusi..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="flex:1;">
                
                <select name="tahun">
                    <option value="">Semua Tahun Lulus</option>
                    <?php
                    $tahun_result = $conn->query("SELECT DISTINCT tahun_lulus FROM alumni WHERE status_verifikasi = 'Verified' ORDER BY tahun_lulus DESC");
                    $selected_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
                    if ($tahun_result) {
                        while ($t = $tahun_result->fetch_assoc()) {
                            $sel = ($selected_tahun == $t['tahun_lulus']) ? 'selected' : '';
                            echo "<option value='" . $t['tahun_lulus'] . "' $sel>" . $t['tahun_lulus'] . "</option>";
                        }
                    }
                    ?>
                </select>
                <select name="sektor">
                    <option value="">Semua Sektor Pekerjaan</option>
                    <?php $sec = isset($_GET['sektor']) ? $_GET['sektor'] : ''; ?>
                    <option value="PNS" <?php echo $sec=='PNS'?'selected':'';?>>PNS / ASN</option>
                    <option value="Swasta" <?php echo $sec=='Swasta'?'selected':'';?>>Swasta</option>
                    <option value="Wirausaha" <?php echo $sec=='Wirausaha'?'selected':'';?>>Wirausaha</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Terapkan</button>
                <?php if ((isset($_GET['search']) && $_GET['search'] != '') || (isset($_GET['tahun']) && $_GET['tahun'] != '') || (isset($_GET['sektor']) && $_GET['sektor'] != '')): ?>
                    <a href="list.php" class="btn btn-secondary" style="border:none;">Reset</a>
                <?php endif; ?>
            </form>

            <?php
            // Pagination & Filter builder
            $batas = 50;
            $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
            if ($halaman < 1) $halaman = 1;

            $search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
            $tahun_filter = isset($_GET['tahun']) ? $conn->real_escape_string(trim($_GET['tahun'])) : '';
            $sektor_filter = isset($_GET['sektor']) ? $conn->real_escape_string(trim($_GET['sektor'])) : '';

            $where_parts = ["status_verifikasi = 'Verified'"];
            if ($search != '') {
                $where_parts[] = "(nama LIKE '%$search%' OR nim LIKE '%$search%' OR program_studi LIKE '%$search%' OR tempat_bekerja LIKE '%$search%')";
            }
            if ($tahun_filter != '') {
                $where_parts[] = "tahun_lulus = '$tahun_filter'";
            }
            if ($sektor_filter != '') {
                $where_parts[] = "jenis_pekerjaan = '$sektor_filter'";
            }
            
            $where_sql = "WHERE " . implode(" AND ", $where_parts);

            $query_total = $conn->query("SELECT COUNT(*) as total FROM alumni $where_sql");
            $jumlah_data = $query_total ? $query_total->fetch_assoc()['total'] : 0;
            $total_halaman = max(1, ceil($jumlah_data / $batas));
            if ($halaman > $total_halaman) $halaman = $total_halaman;
            $halaman_awal = ($halaman - 1) * $batas;

            $result = $conn->query("SELECT * FROM alumni $where_sql ORDER BY tahun_lulus DESC, nama ASC LIMIT $halaman_awal, $batas");

            $param_str = '';
            if ($search) $param_str .= '&search='.urlencode($search);
            if ($tahun_filter) $param_str .= '&tahun='.urlencode($tahun_filter);
            if ($sektor_filter) $param_str .= '&sektor='.urlencode($sektor_filter);
            ?>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <p style="font-size: 0.9rem; color: #64748b; font-weight:500;">
                    Menemukan <strong style="color:var(--text-main);"><?php echo number_format($jumlah_data, 0, ',', '.'); ?></strong> Profil Lulusan
                </p>
            </div>

            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Profil Registran</th>
                            <th width="25%">Pekerjaan & Karir</th>
                            <th width="25%">Kontak Pribadi</th>
                            <th width="20%">Portal Media Sosial</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = $halaman_awal + 1;
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                
                                // Parse badge pekerjaan
                                $badge_class = strtolower($row['jenis_pekerjaan'] ?? '');
                                $jenis_str = $row['jenis_pekerjaan'] ? "<span class='badge {$badge_class}'>".$row['jenis_pekerjaan']."</span>" : "";

                                echo "<tr>";
                                echo "<td>" . $no++ . ".</td>";
                                
                                // Col 1: Profil
                                echo "<td>
                                    <div class='user-meta'>
                                        <div class='user-name'>" . htmlspecialchars($row['nama'] ?? '') . "</div>
                                        <div class='user-nim'>" . htmlspecialchars($row['nim'] ?? '') . " • Lulus: " . htmlspecialchars($row['tahun_lulus'] ?? '') . "</div>
                                        <div style='font-size:0.8rem; color:#6366f1; margin-top:2px; font-weight:500;'>" . htmlspecialchars($row['program_studi'] ?? '') . "</div>
                                    </div>
                                </td>";

                                // Col 2: Karir (points 4,5,6,7,8)
                                echo "<td>";
                                if(!empty($row['pekerjaan']) || !empty($row['tempat_bekerja'])){
                                    echo "<div style='font-weight:600; font-size:0.9rem; color:var(--text-main); display:flex; align-items:center; gap:6px;'>{$jenis_str} " . htmlspecialchars($row['posisi'] ?? $row['pekerjaan']) . "</div>";
                                    echo "<div style='font-size:0.85rem; color:#475569; margin-top:4px;'><i class='fas fa-building' style='color:#94a3b8; width:16px;'></i> " . htmlspecialchars($row['tempat_bekerja'] ?? '-') . "</div>";
                                    if(!empty($row['alamat_bekerja'])){
                                        echo "<div style='font-size:0.8rem; color:#64748b; margin-top:2px;'><i class='fas fa-map-marker-alt' style='color:#94a3b8; width:16px;'></i> " . htmlspecialchars($row['alamat_bekerja']) . "</div>";
                                    }
                                    if(!empty($row['sosmed_tempat_kerja'])){
                                        echo "<div style='font-size:0.8rem; color:#3b82f6; margin-top:2px;'><i class='fas fa-globe' style='width:16px;'></i> " . htmlspecialchars($row['sosmed_tempat_kerja']) . "</div>";
                                    }
                                } else {
                                    echo "<span style='color:#94a3b8; font-size:0.85rem;'>Belum ada data profesional</span>";
                                }
                                echo "</td>";

                                // Col 3: Kontak (points 2,3)
                                echo "<td>
                                    <div class='user-meta'>
                                        " . (empty($row['email']) ? "" : "<div class='contact-pill' title='Email'><i class='fas fa-envelope' style='color:#ef4444;'></i> " . htmlspecialchars($row['email']). "</div>") . "
                                        " . (empty($row['no_hp']) ? "" : "<div class='contact-pill' title='Telepon/WA'><i class='fas fa-phone' style='color:#10b981;'></i> " . htmlspecialchars($row['no_hp']). "</div>") . "
                                        " . (empty($row['email']) && empty($row['no_hp']) ? "<span style='color:#94a3b8; font-size:0.85rem;'>Privat</span>" : "") . "
                                    </div>
                                </td>";

                                // Col 4: Socmed (point 1)
                                echo "<td>
                                    <div class='social-icons'>";
                                        if(!empty($row['linkedin'])) echo "<a href='https://".str_replace(['http://','https://'], '', $row['linkedin'])."' target='_blank' class='linkedin' title='LinkedIn'><i class='fab fa-linkedin'></i></a>";
                                        if(!empty($row['ig'])) echo "<a href='https://instagram.com/".str_replace('@', '', $row['ig'])."' target='_blank' class='ig' title='Instagram'><i class='fab fa-instagram'></i></a>";
                                        if(!empty($row['fb'])) echo "<a href='".$row['fb']."' target='_blank' class='fb' title='Facebook'><i class='fab fa-facebook'></i></a>";
                                        if(!empty($row['tiktok'])) echo "<a href='https://tiktok.com/".str_replace('@', '', $row['tiktok'])."' target='_blank' class='tiktok' title='TikTok'><i class='fab fa-tiktok'></i></a>";
                                        
                                        if(empty($row['linkedin']) && empty($row['ig']) && empty($row['fb']) && empty($row['tiktok'])){
                                            echo "<span style='color:#94a3b8; font-size:0.85rem;'>Tidak ditambahkan</span>";
                                        }
                                echo "</div>
                                </td>";

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center' style='padding: 60px; color: #94a3b8;'><i class='fas fa-box-open' style='font-size:2rem; margin-bottom:10px;'></i><br>Tidak ada data alumni untuk kriteria filter ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination (Premium layout) -->
            <?php if($total_halaman > 1): ?>
            <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center; background:#fff; padding:12px 20px; border-radius:12px; border:1px solid var(--border);">
                <p style="font-size: 0.9rem; color: #64748b;">Menampilkan halaman <strong><?php echo $halaman; ?></strong> dari <strong><?php echo $total_halaman; ?></strong></p>
                <div style="display: flex; gap: 8px;">
                    <?php if ($halaman > 1): ?>
                        <a href="?halaman=1<?php echo $param_str; ?>" class="btn btn-secondary btn-sm">Awal</a>
                        <a href="?halaman=<?php echo $halaman - 1 . $param_str; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-chevron-left"></i> Prev</a>
                    <?php endif; ?>
                    
                    <span class="btn btn-primary btn-sm" style="pointer-events:none;"><?php echo $halaman; ?></span>
                    
                    <?php if ($halaman < $total_halaman): ?>
                        <a href="?halaman=<?php echo $halaman + 1 . $param_str; ?>" class="btn btn-secondary btn-sm">Next <i class="fas fa-chevron-right"></i></a>
                        <a href="?halaman=<?php echo $total_halaman . $param_str; ?>" class="btn btn-secondary btn-sm">Akhir</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
        </main>
    </div>
</body>
</html>