<?php
session_start();
require_once "../config/koneksi.php";

/* Filter dengan sanitasi */
$status_filter  = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$tanggal_filter = isset($_GET['tanggal']) ? mysqli_real_escape_string($conn, $_GET['tanggal']) : '';

$where = "";
if (!empty($status_filter)) {
    $where .= " AND ps.status_pesanan = '$status_filter'";
}
if (!empty($tanggal_filter)) {
    if (DateTime::createFromFormat('Y-m-d', $tanggal_filter) !== false) {
        $where .= " AND DATE(ps.tanggal_pesan) = '$tanggal_filter'";
    }
}

/* Query utama */
$query = "
    SELECT
        ps.pesanan_id AS pembayaran_id,
        ps.pesanan_id,
        ps.no_pesanan,
        ps.total_bayar,
        ps.status_pesanan,
        ps.tanggal_pesan,
        COALESCE(p.metode_pembayaran, 'Belum dibayar') AS metode,
        p.bukti_pembayaran,
        u.nama_lengkap,
        u.email
    FROM pesanan ps
    JOIN users u ON ps.user_id = u.user_id
    LEFT JOIN pembayaran p ON p.pesanan_id = ps.pesanan_id
    WHERE 1=1 $where
    ORDER BY ps.tanggal_pesan DESC
";

$data = mysqli_query($conn, $query);
if (!$data) {
    die("Query Error: " . mysqli_error($conn));
}

/* Statistik */
$total_pembayaran = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pesanan"));
$pending_verifikasi = mysqli_num_rows(mysqli_query(
    $conn,
    "SELECT * FROM pesanan WHERE status_pesanan='pending'"
));
$selesai = mysqli_num_rows(mysqli_query(
    $conn,
    "SELECT * FROM pesanan WHERE status_pesanan!='pending'"
));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manajemen Pembayaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(245, 87, 108, 0.3);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        
        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 30px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 1px, transparent 1px);
            background-size: 25px 25px;
            animation: float 20s linear infinite;
        }
        
        @keyframes float {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .header h1 {
            font-size: 2.8rem;
            margin-bottom: 10px;
            position: relative;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
            position: relative;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 25px;
            background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(245, 87, 108, 0.1);
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(245, 87, 108, 0.2);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #f093fb, #f5576c);
        }
        
        .stat-card:nth-child(2)::before {
            background: linear-gradient(90deg, #ec4899, #db2777);
        }
        
        .stat-card:nth-child(3)::before {
            background: linear-gradient(90deg, #f472b6, #be185d);
        }
        
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-card:nth-child(2) i {
            background: linear-gradient(135deg, #ec4899, #db2777);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-card:nth-child(3) i {
            background: linear-gradient(135deg, #f472b6, #be185d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-number {
            font-size: 2.8rem;
            font-weight: bold;
            color: #831843;
            margin: 10px 0;
            display: block;
        }
        
        .stat-label {
            color: #9d174d;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .filter-section {
            background: white;
            padding: 25px;
            margin: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(245, 87, 108, 0.08);
            border: 1px solid #fce7f3;
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            align-items: end;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 8px;
            color: #831843;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .form-control {
            padding: 14px;
            border: 2px solid #fbcfe8;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #fdf2f8;
            color: #831843;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #f472b6;
            background: white;
            box-shadow: 0 0 0 3px rgba(244, 114, 182, 0.1);
        }
        
        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 87, 108, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            color: white;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(219, 39, 119, 0.4);
        }
        
        .table-container {
            background: white;
            margin: 20px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(245, 87, 108, 0.08);
            border: 1px solid #fce7f3;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }
        
        thead {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        th {
            padding: 20px 15px;
            text-align: left;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }
        
        tbody tr {
            border-bottom: 1px solid #fce7f3;
            transition: all 0.3s;
        }
        
        tbody tr:hover {
            background: #fdf2f8;
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(245, 87, 108, 0.1);
        }
        
        td {
            padding: 18px 15px;
            color: #831843;
        }
        
        .badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background: linear-gradient(135deg, #34d399, #10b981);
            color: white;
        }
        
        .badge-warning {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
        }
        
        .badge-secondary {
            background: linear-gradient(135deg, #f472b6, #ec4899);
            color: white;
        }
        
        .btn-action {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
        }
        
        .btn-info {
            background: linear-gradient(135deg, #ec4899, #db2777);
            color: white;
        }
        
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(219, 39, 119, 0.4);
        }
        
        .btn-print {
            background: linear-gradient(135deg, #e91e63, #c2185b);
            color: white;
        }
        
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(194, 24, 91, 0.4);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9d174d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.7;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .table-container {
                overflow-x: auto;
                margin: 10px;
            }
            
            table {
                min-width: 800px;
            }
            
            .btn-action {
                padding: 6px 12px;
                font-size: 0.75rem;
                margin-right: 3px;
            }
        }
        
        /* Tambahan untuk input date */
        input[type="date"] {
            color: #831843;
        }
        
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(30%) sepia(50%) saturate(2000%) hue-rotate(300deg);
        }
        
        select option {
            color: #831843;
            padding: 10px;
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #fdf2f8;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #ec4899, #db2777);
        }
        
        /* Untuk grouping tombol aksi */
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-credit-card"></i> Manajemen Pembayaran</h1>
            <p>Kelola dan pantau semua transaksi pembayaran pelanggan</p>
        </div>
        
        <!-- Statistik -->
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-receipt"></i>
                <span class="stat-number"><?php echo $total_pembayaran; ?></span>
                <span class="stat-label">Total Pesanan</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <span class="stat-number"><?php echo $pending_verifikasi; ?></span>
                <span class="stat-label">Pending Verifikasi</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <span class="stat-number"><?php echo $selesai; ?></span>
                <span class="stat-label">Pesanan Selesai</span>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="" class="filter-form">
                <div class="form-group">
                    <label for="status"><i class="fas fa-filter"></i> Filter Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="pending" <?php echo ($status_filter == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="selesai" <?php echo ($status_filter == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tanggal"><i class="fas fa-calendar"></i> Filter Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?php echo $tanggal_filter; ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Terapkan Filter
                    </button>
                    <a href="?" class="btn btn-secondary" style="margin-top: 10px;">
                        <i class="fas fa-redo"></i> Reset Filter
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Tabel Data -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No Pesanan</th>
                        <th>Nama Pelanggan</th>
                        <th>Email</th>
                        <th>Total Bayar</th>
                        <th>Metode Pembayaran</th>
                        <th>Status</th>
                        <th>Tanggal Pesan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['no_pesanan']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><strong>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['metode']); ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    if ($row['status_pesanan'] == 'selesai') {
                                        $status_class = 'badge-success';
                                    } elseif ($row['status_pesanan'] == 'pending') {
                                        $status_class = 'badge-warning';
                                    } else {
                                        $status_class = 'badge-secondary';
                                    }
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($row['status_pesanan']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal_pesan'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <!-- Tombol Detail -->
                                        <a href="detail.php?id=<?php echo $row['pesanan_id']; ?>" class="btn-action btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        
                                        <!-- Tombol Cetak Nota -->
                                        <a href="cetak_nota.php?id=<?php echo $row['pesanan_id']; ?>" class="btn-action btn-print" target="_blank">
                                            <i class="fas fa-print"></i> Cetak
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h3>Tidak ada data pesanan</h3>
                                    <p>Belum ada transaksi yang tercatat</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Tambahkan efek hover pada stat card
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
                this.style.boxShadow = '0 20px 40px rgba(245, 87, 108, 0.3)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 10px 30px rgba(245, 87, 108, 0.1)';
            });
        });
        
        // Format tanggal input
        const tanggalInput = document.getElementById('tanggal');
        if (tanggalInput) {
            tanggalInput.addEventListener('change', function(e) {
                console.log('Tanggal dipilih:', e.target.value);
            });
        }
        
        // Tambahkan animasi untuk baris tabel
        document.querySelectorAll('tbody tr').forEach((row, index) => {
            row.style.animationDelay = `${index * 0.05}s`;
        });
        
        // Warna dinamis untuk badge berdasarkan status
        document.querySelectorAll('.badge').forEach(badge => {
            const status = badge.textContent.trim().toLowerCase();
            if (status === 'selesai') {
                badge.style.background = 'linear-gradient(135deg, #34d399, #10b981)';
            } else if (status === 'pending') {
                badge.style.background = 'linear-gradient(135deg, #fbbf24, #f59e0b)';
            } else {
                badge.style.background = 'linear-gradient(135deg, #f472b6, #ec4899)';
            }
        });
        
        // Auto focus pada filter status jika ada filter
        const statusSelect = document.getElementById('status');
        if (statusSelect && '<?php echo $status_filter; ?>' !== '') {
            statusSelect.focus();
        }
        
        // Konfirmasi reset filter
        const resetBtn = document.querySelector('a[href="?"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                if (!confirm('Reset semua filter?')) {
                    e.preventDefault();
                }
            });
        }
    </script>
</body>
</html>
