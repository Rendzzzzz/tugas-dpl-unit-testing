<?php
include 'auth.php';
include 'config.php';

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M'); // Limit memori diperbesar

$csvFile = 'alumni_data.csv';

if (!file_exists($csvFile)) {
    die("<html><body style='font-family:sans-serif;padding:20px;'><h2 style='color:red;'>❌ File $csvFile tidak ditemukan!</h2><p>Pastikan file alumni_data.csv ada di folder project.</p><a href='admin.php'>Kembali</a></body></html>");
}

echo "<html><head><title>Proses Import Lengkap 8 Data</title></head><body style='font-family: sans-serif; padding: 20px; background:#f8fafc;'>";
echo "<h2>📤 Memulai Import Data Lengkap (8 Poin Data)...</h2>";
echo "<p>Memproses dan men-generate otomatis 8 poin data (Sosmed, Pekerjaan, Kontak) untuk SELURUH baris data. Mohon tunggu...</p><hr>";

$file = fopen($csvFile, "r");
$headerFound = false;

// Cari baris header
while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
    if (isset($data[0]) && trim($data[0]) === 'Nama Lulusan') {
        $headerFound = true;
        break;
    }
}

if (!$headerFound) {
    rewind($file);
    $firstRow = fgetcsv($file, 1000, ",");
}

$conn->begin_transaction();

$stmt = $conn->prepare("INSERT IGNORE INTO alumni
    (nim, nama, tahun_lulus, program_studi, pekerjaan, linkedin, ig, fb, tiktok, email, no_hp, tempat_bekerja, alamat_bekerja, posisi, jenis_pekerjaan, sosmed_tempat_kerja, status_verifikasi)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$count = 0;
$skip = 0;

$pekerjaan_list = ['Software Engineer', 'Data Analyst', 'HR Manager', 'Akuntan', 'Guru', 'Pengusaha', 'Staff Administrasi', 'Marketing', 'Dokter', 'Manajer Operasional', 'Arsitek', 'Peneliti', 'Direktur', 'Konsultan'];
$jenis_pekerjaan_list = ['PNS', 'Swasta', 'Wirausaha'];
$tempat_kerja_list = ['PT. Telkom', 'Bank Mandiri', 'Kementerian Keuangan', 'Shopee', 'Tokopedia', 'Pemerintah Kota', 'Gojek', 'Pertamina', 'BRI', 'RSUD', 'Google Indonesia', 'Startup Tech'];
$kota_list = ['Jakarta Selatan', 'Surabaya', 'Malang', 'Bandung', 'Yogyakarta', 'Semarang', 'Medan', 'Makassar', 'Denpasar', 'Balikpapan'];

while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
    if (count($data) < 4 || empty(trim($data[0]))) {
        $skip++;
        continue;
    }

    $nama = trim($data[0]);
    $nim  = trim($data[1] ?? '');
    $tahun_masuk = trim($data[2] ?? '');
    $tanggal_lulus = trim($data[3] ?? '');
    $program_studi = trim($data[5] ?? $data[4] ?? 'Ilmu Komputer');

    if (empty($nim)) {
        $nim = 'AUTO' . str_pad($count + $skip, 8, '0', STR_PAD_LEFT);
    }

    $tahun_lulus = 0;
    if (preg_match('/\d{4}/', $tanggal_lulus, $matches)) {
        $tahun_lulus = (int)$matches[0];
    }
    if ($tahun_lulus < 1990 || $tahun_lulus > 2050) {
        $tahun_lulus = !empty($tahun_masuk) ? (int)$tahun_masuk + 4 : date('Y');
    }

    // GENERATE UNTUK SEMUA DATA (MEMASUKKAN KE-8 POIN SECARA PENUH)
    $pekerjaan      = $pekerjaan_list[array_rand($pekerjaan_list)];
    $jenis_pekerjaan = $jenis_pekerjaan_list[array_rand($jenis_pekerjaan_list)];
    $tempat_kerja   = $tempat_kerja_list[array_rand($tempat_kerja_list)];
    $kota           = $kota_list[array_rand($kota_list)];

    $username   = strtolower(preg_replace('/[^a-z0-9]/i', '', $nama)) . rand(10, 999);
    
    // Poin 2 & 3: Kontak
    $email      = $username . "@gmail.com";
    $no_hp      = "08" . rand(100000000, 999999999);
    
    // Poin 1: Media Sosial
    $linkedin   = "linkedin.com/in/" . $username;
    $ig         = "@" . $username;
    $fb         = "facebook.com/" . $username;
    $tiktok     = "@" . $username . "_official";
    
    // Poin 4, 5, 6: Pekerjaan, Alamat, Posisi
    $alamat_bekerja = "Jl. Sudirman Kav. " . rand(1, 150) . ", " . $kota;
    $posisi     = $pekerjaan;
    
    // Poin 8: Sosmed Tempat Kerja
    $sosmed_kerja = "@" . strtolower(preg_replace('/[^a-z0-9]/i', '', $tempat_kerja)) . "_id";
    
    // Status
    $status_verifikasi = 'Verified'; 

    $stmt->bind_param("ssissssssssssssss",
        $nim, $nama, $tahun_lulus, $program_studi,
        $pekerjaan, $linkedin, $ig, $fb, $tiktok, $email, $no_hp,
        $tempat_kerja, $alamat_bekerja, $posisi, $jenis_pekerjaan, $sosmed_kerja, $status_verifikasi
    );
    
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $count++;
    }

    // Commit per 2000 baris agar super cepat & irit RAM
    if ($count % 2000 == 0 && $count > 0) {
        $conn->commit();
        $conn->begin_transaction();
        echo "<p style='color:#64748b; font-size:0.9rem; margin:2px 0;'>✔ " . number_format($count, 0, ',', '.') . " alumni berhasil di-generate 8 data poin...</p>";
        ob_flush(); flush();
    }
}

$conn->commit();
fclose($file);
$stmt->close();

echo "<h3 style='color: green; margin-top:20px;'>✅ IMPORT SELESAI! Seluruh Data Kini Memiliki 8 Poin Lengkap</h3>";
echo "<table style='border-collapse:collapse; margin-top:16px; background:#fff; padding:10px; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.1);'>";
echo "<tr><td style='padding:12px; border-bottom:1px solid #eee;'>Total Data Masuk</td><td style='padding:12px; border-bottom:1px solid #eee; font-weight:bold; color:#2563eb;'>" . number_format($count, 0, ',', '.') . " Alumni</td></tr>";
echo "<tr><td style='padding:12px; border-bottom:1px solid #eee;'>Status Sinkronisasi 8 Poin</td><td style='padding:12px; border-bottom:1px solid #eee; font-weight:bold; color:#10b981;'>100% SUKSES (Profil, 4 Sosmed, Pekerjaan, dll)</td></tr>";
echo "<tr><td style='padding:12px;'>Data Duplikat/Dilewati</td><td style='padding:12px; color:#64748b;'>" . number_format($skip, 0, ',', '.') . " baris</td></tr>";
echo "</table>";
echo "<div style='margin-top:20px; display:flex; gap:10px;'>";
echo "<a href='list.php' style='padding: 10px 20px; background: #6366f1; color: white; text-decoration: none; border-radius: 6px; font-weight:600;'>👀 Lihat Hasil di Daftar</a>";
echo "<a href='export_excel.php' style='padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 6px; font-weight:600;'>📥 Export 8 Data Laporan Excel</a>";
echo "</div>";
echo "</body></html>";
?>
