<?php
include "koneksi.php";

/* FILTER */
$mode  = $_GET['mode'] ?? 'bulanan'; // mingguan | bulanan
$tahun = $_GET['tahun'] ?? date('Y');
$bulan = $_GET['bulan'] ?? date('m');

/* ======================
   TOTAL PEMBELANJAAN
====================== */

/* MINGGUAN (7 hari terakhir) */
$totalMingguan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(harga_barang) AS total
    FROM data_orderan_reseller
    WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
"))['total'] ?? 0;

/* BULANAN */
$totalBulanan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(harga_barang) AS total
    FROM data_orderan_reseller
    WHERE YEAR(tanggal)='$tahun'
    AND MONTH(tanggal)='$bulan'
"))['total'] ?? 0;

/* TAHUNAN */
$totalTahunan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(harga_barang) AS total
    FROM data_orderan_reseller
    WHERE YEAR(tanggal)='$tahun'
"))['total'] ?? 0;

/* ======================
   DATA GRAFIK
====================== */

$labels = [];
$data   = [];

if ($mode === 'mingguan') {

    $q = mysqli_query($conn, "
        SELECT DATE(tanggal) tgl, SUM(harga_barang) total
        FROM data_orderan_reseller
        WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(tanggal)
        ORDER BY tgl
    ");

    while ($r = mysqli_fetch_assoc($q)) {
        $labels[] = date('d M', strtotime($r['tgl']));
        $data[]   = (int)$r['total'];
    }
} else {

    $bulanNama = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $grafik = array_fill(1, 12, 0);

    $q = mysqli_query($conn, "
        SELECT MONTH(tanggal) bln, SUM(harga_barang) total
        FROM data_orderan_reseller
        WHERE YEAR(tanggal)='$tahun'
        GROUP BY MONTH(tanggal)
    ");

    while ($r = mysqli_fetch_assoc($q)) {
        $grafik[(int)$r['bln']] = (int)$r['total'];
    }

    $labels = $bulanNama;
    $data   = array_values($grafik);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - Saung Syar'i</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #4f46e5;
            --bg: #f4f6fb;
            --card: #fff;
            --border: #e5e7eb
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: Segoe UI;
            background: var(--bg)
        }

        .app {
            display: flex;
            min-height: 100vh
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #111827, #1f2937);
            color: #fff;
            padding: 30px 20px
        }

        .logo {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 40px
        }

        .sidebar nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            margin-bottom: 8px;
            color: #d1d5db;
            text-decoration: none;
            border-radius: 10px;
            font-size: 17px;
            transition: all 0.25s ease;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background: var(--primary);
            color: #fff
        }

        /* CONTENT */
        .content {
            flex: 1;
            padding: 40px
        }

        .card {
            background: var(--card);
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
            margin-bottom: 30px
        }

        select,
        input,
        button {
            width: 100%;
            height: 80px;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid var(--border);
            margin-top: 12px;
        }

        button {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            font-weight: 600;
            border: none;
            cursor: pointer
        }

        .error {
            color: red;
            margin-top: 10px
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse
        }

        thead {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid var(--border)
        }

        .money {
            color: #16a34a;
            font-weight: 600
        }

        .logo-saung {
            height: 145px;
            /* atur tinggi logo */
            width: auto;
            object-fit: contain;
            border-radius: 20px;
            margin-bottom: 50px;
        }

        .cards {
            display: flex;
            gap: 20px;
            margin-bottom: 30px
        }

        .card-box {
            flex: 1;
            color: #fff;
            padding: 25px;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .15)
        }

        .card-box h4 {
            margin: 0;
            font-weight: 500
        }

        .card-box h2 {
            margin: 10px 0 0
        }

        .c1 {
            background: linear-gradient(135deg, #f97316, #ea580c)
        }

        .c2 {
            background: linear-gradient(135deg, #6366f1, #4f46e5)
        }

        .c3 {
            background: linear-gradient(135deg, #22c55e, #16a34a)
        }

        .card {
            background: #fff;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08)
        }

        .logo-saung {
            height: 145px;
            /* atur tinggi logo */
            width: auto;
            object-fit: contain;
            border-radius: 20px;
            margin-bottom: 50px;
        }
    </style>
</head>

<body>
    <div class="app">

        <aside class="sidebar">
            <img
                src="foto/saungSyari.png"
                alt="Logo Saung Syar'i"
                class="logo-saung" />

            <nav>
                <a href="dashboard.php" class="active">📊 Dashboard</a>
                <a href="admin.php">📦 Order</a>
                <a href="data-reseller.php">🛒 Reseller</a>
                <a href="owner.php" class="sidebar-link">👑 Owner</a>
            </nav>
        </aside>

        <main class="content">

            <div class="cards">
                <div class="card-box c1">
                    <h4>Total Mingguan</h4>
                    <h2>Rp <?= number_format($totalMingguan) ?></h2>
                </div>
                <div class="card-box c2">
                    <h4>Total Bulanan</h4>
                    <h2>Rp <?= number_format($totalBulanan) ?></h2>
                </div>
                <div class="card-box c3">
                    <h4>Total Tahunan</h4>
                    <h2>Rp <?= number_format($totalTahunan) ?></h2>
                </div>
            </div>

            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <h3>Grafik Pembelanjaan</h3>
                    <form method="GET">
                        <select name="mode" onchange="this.form.submit()">
                            <option value="bulanan" <?= $mode == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                            <option value="mingguan" <?= $mode == 'mingguan' ? 'selected' : '' ?>>Mingguan</option>
                        </select>
                    </form>
                </div>

                <canvas id="chart" height="120"></canvas>
            </div>

        </main>
    </div>

    <script>
        new Chart(document.getElementById('chart'), {
            type: 'line',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: <?= json_encode($data) ?>,
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>
