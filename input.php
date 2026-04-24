<?php
include 'auth.php';
include 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $tahun_lulus = (int) ($_POST['tahun_lulus'] ?? 0);
    $program_studi = trim($_POST['program_studi'] ?? '');
    $pekerjaan = trim($_POST['pekerjaan'] ?? '');

    // 8 Required Fields
    $linkedin = trim($_POST['linkedin'] ?? '');
    $ig = trim($_POST['ig'] ?? '');
    $fb = trim($_POST['fb'] ?? '');
    $tiktok = trim($_POST['tiktok'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $tempat_bekerja = trim($_POST['tempat_bekerja'] ?? '');
    $alamat_bekerja = trim($_POST['alamat_bekerja'] ?? '');
    $posisi = trim($_POST['posisi'] ?? '');
    $jenis_pekerjaan = !empty($_POST['jenis_pekerjaan']) ? $_POST['jenis_pekerjaan'] : null;
    $sosmed_tempat_kerja = trim($_POST['sosmed_tempat_kerja'] ?? '');

    if (empty($nim) || empty($nama)) {
        $message = "<div class='alert error'><i class='fas fa-exclamation-triangle'></i> NIM dan Nama Lengkap wajib diisi!</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO alumni (nim, nama, tahun_lulus, program_studi, pekerjaan, linkedin, ig, fb, tiktok, email, no_hp, tempat_bekerja, alamat_bekerja, posisi, jenis_pekerjaan, sosmed_tempat_kerja) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssisssssssssssss", $nim, $nama, $tahun_lulus, $program_studi, $pekerjaan, $linkedin, $ig, $fb, $tiktok, $email, $no_hp, $tempat_bekerja, $alamat_bekerja, $posisi, $jenis_pekerjaan, $sosmed_tempat_kerja);
            if ($stmt->execute()) {
                $message = "<div class='alert success'><i class='fas fa-check-circle'></i> Data alumni berhasil disimpan! Status: Menunggu Verifikasi.</div>";
            } else {
                if ($conn->errno == 1062) {
                    $message = "<div class='alert error'><i class='fas fa-times-circle'></i> NIM <strong>$nim</strong> sudah terdaftar dalam sistem.</div>";
                } else {
                    $message = "<div class='alert error'><i class='fas fa-times-circle'></i> Error: " . $stmt->error . "</div>";
                }
            }
            $stmt->close();
        } else {
            $message = "<div class='alert error'><i class='fas fa-times-circle'></i> Sistem Error: " . $conn->error . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Alumni - Premium Track</title>
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
                <a href="index.php"><i class="fas fa-chart-pie"></i> Dashboard</a>
                <a href="input.php" class="active"><i class="fas fa-user-plus"></i> Input Data</a>
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
            <div class="page-header" style="margin-bottom: 24px;">
                <div class="page-title">
                    <h2>Registrasi Data Alumni</h2>
                    <p>Lengkapi profil riwayat lulusan, pekerjaan, serta kontak alumni secara detail.</p>
                </div>
                <a href="list.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
            </div>

            <?php echo $message; ?>

            <form action="input.php" method="POST">
                <div class="card-panel" style="padding-bottom: 16px;">
                    <h3 class="section-title"><i class="fas fa-id-card"></i> Identitas & Akademik</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nama Lengkap (Sesuai Ijazah) <span style="color:var(--danger)">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Cth. John Doe, S.Kom" required>
                        </div>
                        <div class="form-group">
                            <label>NIM (Nomor Induk Mahasiswa) <span style="color:var(--danger)">*</span></label>
                            <input type="text" name="nim" class="form-control" placeholder="Cth. 20111223" required>
                        </div>
                        <div class="form-group">
                            <label>Tahun Lulus <span style="color:var(--danger)">*</span></label>
                            <input type="number" name="tahun_lulus" class="form-control" value="<?php echo date('Y'); ?>" min="1990" required>
                        </div>
                        <div class="form-group">
                            <label>Program Studi Utama <span style="color:var(--danger)">*</span></label>
                            <div class="input-with-icon">
                                <i class="fas fa-book-open"></i>
                                <input type="text" name="program_studi" class="form-control" placeholder="Teknik Informatika" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-panel" style="padding-bottom: 16px;">
                    <h3 class="section-title"><i class="fas fa-user-circle"></i> Kontak Pribadi</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Alamat Email (Aktif)</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" class="form-control" placeholder="alumni@email.com">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>No. HP / WhatsAppAktif</label>
                            <div class="input-with-icon">
                                <i class="fab fa-whatsapp" style="color: #25D366;"></i>
                                <input type="text" name="no_hp" class="form-control" placeholder="081234567890">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-panel" style="padding-bottom: 16px;">
                    <h3 class="section-title"><i class="fas fa-hashtag"></i> Portal Media Sosial</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Profil LinkedIn</label>
                            <div class="input-with-icon">
                                <i class="fab fa-linkedin" style="color: #0A66C2;"></i>
                                <input type="text" name="linkedin" class="form-control" placeholder="linkedin.com/in/username">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Instagram</label>
                            <div class="input-with-icon">
                                <i class="fab fa-instagram" style="color: #E1306C;"></i>
                                <input type="text" name="ig" class="form-control" placeholder="@username">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Facebook</label>
                            <div class="input-with-icon">
                                <i class="fab fa-facebook" style="color: #1877F2;"></i>
                                <input type="text" name="fb" class="form-control" placeholder="facebook.com/username">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>TikTok</label>
                            <div class="input-with-icon">
                                <i class="fab fa-tiktok" style="color: #000000;"></i>
                                <input type="text" name="tiktok" class="form-control" placeholder="@username">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-panel" style="padding-bottom: 16px; border-left: 4px solid var(--success);">
                    <h3 class="section-title"><i class="fas fa-briefcase"></i> Data Pekerjaan Profesional</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Status / Jenis Pekerjaan</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user-tie"></i>
                                <select name="jenis_pekerjaan" class="form-control">
                                    <option value="">-- Pilih Status Sektor --</option>
                                    <option value="PNS">Pegawai Negeri / ASN (PNS)</option>
                                    <option value="Swasta">Pegawai Swasta</option>
                                    <option value="Wirausaha">Wirausahawan / Freelance</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Judul Pekerjaan Saat Ini (General)</label>
                            <div class="input-with-icon">
                                <i class="fas fa-tag"></i>
                                <input type="text" name="pekerjaan" class="form-control" placeholder="Cth: Programmer, Guru, Pengusaha F&B">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Posisi Jabatan Detail</label>
                            <div class="input-with-icon">
                                <i class="fas fa-sitemap"></i>
                                <input type="text" name="posisi" class="form-control" placeholder="Cth: Lead Backend Dev / Supervisor">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Nama Tempat Bekerja</label>
                            <div class="input-with-icon">
                                <i class="fas fa-building"></i>
                                <input type="text" name="tempat_bekerja" class="form-control" placeholder="PT Maju Sejahtera Tbk">
                            </div>
                        </div>
                        
                        <div class="form-group full">
                            <label>Sosial Media Instansi / Perusahaan</label>
                            <div class="input-with-icon">
                                <i class="fas fa-globe"></i>
                                <input type="text" name="sosmed_tempat_kerja" class="form-control" placeholder="IG/LinkedIn/Web perusahaan (cth: @gojekindonesia)">
                            </div>
                        </div>

                        <div class="form-group full">
                            <label>Alamat Lengkap Tempat Bekerja</label>
                            <textarea name="alamat_bekerja" class="form-control" rows="3" placeholder="Jl. Jend. Sudirman Kav 20, Jakarta Selatan..."></textarea>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 16px; margin-bottom: 40px;">
                    <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-size: 1.05rem;"><i class="fas fa-save"></i> Kirim & Daftarkan Alumni</button>
                    <button type="reset" class="btn btn-secondary" style="padding: 12px 24px;"><i class="fas fa-undo"></i> Reset Form</button>
                </div>

            </form>
        </main>
    </div>
</body>
</html>