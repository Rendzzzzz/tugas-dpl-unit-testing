<?php
session_start();
// FIX: Gunakan session key yang sama dengan auth.php yaitu 'logged_in'
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Atur memori dan batas waktu karena data bisa mencapai 142.000
ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

// Filter tahun jika ada
$filter_tahun = isset($_GET['tahun']) ? trim($_GET['tahun']) : 'all';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : 'all';

// Set nama file
$filename = "Export_Data_Alumni_" . date('Ymd_His') . ".csv";

// Atur header HTTP agar browser mengunduh file ini sebagai CSV (Bisa dibuka di Excel)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis BOM (Byte Order Mark) untuk UTF-8 agar Excel mengenali karakter dengan benar
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header Kolom di Excel
fputcsv($output, array(
    'No', 'NIM', 'Nama Lengkap', 'Tahun Lulus', 'Program Studi',
    'Email', 'No. HP', 'LinkedIn', 'Instagram', 'Facebook', 'TikTok',
    'Pekerjaan', 'Status Kerja', 'Tempat Bekerja', 'Posisi/Jabatan',
    'Alamat Bekerja', 'Sosmed Tempat Kerja', 'Status Verifikasi', 'Tanggal Daftar'
));

// Build query dengan filter
$conditions = [];
if ($filter_tahun !== 'all' && !empty($filter_tahun)) {
    $tahun_safe = $conn->real_escape_string($filter_tahun);
    $conditions[] = "tahun_lulus = '$tahun_safe'";
}
if ($filter_status !== 'all' && !empty($filter_status)) {
    $status_safe = $conn->real_escape_string($filter_status);
    $conditions[] = "status_verifikasi = '$status_safe'";
}

$where = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";
$sql = "SELECT * FROM alumni $where ORDER BY tahun_lulus DESC, nama ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $no++,
            $row['nim'] ?? '',
            $row['nama'] ?? '',
            $row['tahun_lulus'] ?? '',
            $row['program_studi'] ?? '',
            $row['email'] ?? '',
            $row['no_hp'] ?? '',
            $row['linkedin'] ? $row['linkedin'] : '-',
            $row['ig'] ? $row['ig'] : '-',
            $row['fb'] ? $row['fb'] : '-',
            $row['tiktok'] ? $row['tiktok'] : '-',
            $row['pekerjaan'] ? $row['pekerjaan'] : '-',
            $row['jenis_pekerjaan'] ? $row['jenis_pekerjaan'] : '-',
            $row['tempat_bekerja'] ? $row['tempat_bekerja'] : '-',
            $row['posisi'] ? $row['posisi'] : '-',
            $row['alamat_bekerja'] ? $row['alamat_bekerja'] : '-',
            $row['sosmed_tempat_kerja'] ? $row['sosmed_tempat_kerja'] : '-',
            $row['status_verifikasi'] ?? 'Pending',
            $row['created_at'] ?? ''
        ));
    }
}

fclose($output);
exit();
?>
