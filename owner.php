<?php
session_start();
include "koneksi.php";


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: owner.php");
    exit;
}


/* PASSWORD PER NAMA */
$password_nama = [
    "OWNER" => "owner1111"
];

$step  = $_SESSION['login_owner'] ?? 'pilih_nama';
$error = "";


/* STEP 1 : PILIH NAMA */
if (isset($_POST['pilih_nama'])) {
    $_SESSION['nama_pilih'] = $_POST['nama'];
    unset($_SESSION['login_owner']); // reset login
    $step = "password";
}

/* STEP 2 : CEK PASSWORD */
if (isset($_POST['cek_password'])) {
    $nama_pilih = $_SESSION['nama_pilih'] ?? '';
    $password   = $_POST['password'];

    if (!isset($password_nama[$nama_pilih])) {
        session_destroy();
        header("Location: owner.php");
        exit;
    }

    if ($password === $password_nama[$nama_pilih]) {
        $_SESSION['login_owner'] = 'tampil_data';
        $step = "tampil_data";
    } else {
        $step  = "password";
        $error = "Password salah!";
    }
}

/* ================================
   DATA HANYA JALAN SAAT LOGIN OK
================================ */
if (
    ($step === "tampil_data") &&
    isset($_SESSION['login_owner']) &&
    isset($_SESSION['nama_pilih'])
) {


    $tahun = $_GET['tahun'] ?? date('Y');
    $bulan = $_GET['bulan'] ?? date('m');

    $nama  = mysqli_real_escape_string($conn, $_SESSION['nama_pilih']);

    $totalBulan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(fee_rupiah_owner) AS total
    FROM data_orderan_reseller
    WHERE YEAR(tanggal)='$tahun'
    AND MONTH(tanggal)='$bulan'
    "))['total'] ?? 0;




    $totalTahun = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(fee_rupiah_owner) AS total
    FROM data_orderan_reseller
    WHERE YEAR(tanggal)='$tahun'
    "))['total'] ?? 0;




    $totalBuyBulan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(harga_barang) AS total
    FROM data_orderan_reseller
    WHERE YEAR(tanggal)='$tahun'
    AND MONTH(tanggal)='$bulan'
    "))['total'] ?? 0;


    $totalBuyTahun = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(harga_barang) AS total
    FROM data_orderan_reseller
    WHERE YEAR(tanggal)='$tahun'
    "))['total'] ?? 0;





    /* DATA GRAFIK BULANAN */
    $grafik = array_fill(1, 12, 0);
    $q = mysqli_query($conn, "
    SELECT MONTH(tanggal) AS bulan, SUM(fee_rupiah_owner) AS total
    FROM data_orderan_reseller
    WHERE YEAR(tanggal)='$tahun'
    GROUP BY MONTH(tanggal)
    ");



    while ($r = mysqli_fetch_assoc($q)) {
        $grafik[(int)$r['bulan']] = (int)$r['total'];
    }

    /* DATA TABEL */
    $data = mysqli_query($conn, "
    SELECT *
    FROM data_orderan_reseller
    WHERE YEAR(tanggal)='$tahun'
    ORDER BY tanggal DESC
    ");

}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Data Owner - Saung-Syar'i</title>
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
                <a href="dashboard.php">📊 Dashboard</a>
                <a href="admin.php">📦 Order</a>
                <a href="data-reseller.php">🛒 Reseller</a>
                <a href="owner.php" class="active">👑 Owner</a>
            </nav>
        </aside>

        <main class="content">

            <?php if ($step === "pilih_nama") { ?>

                <div class="card">
                    <h3>Pilih Nama Reseller</h3>
                    <form method="POST">
                        <select name="nama" required>
                            <option value="OWNER">Owner</option>
                        </select>
                        <button name="pilih_nama">Lanjut</button>
                    </form>
                </div>

            <?php } elseif ($step === "password") { ?>

                <div class="card">
                    <h3>Password <?= $_SESSION['nama_pilih'] ?></h3>
                    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
                    <form method="POST">
                        <input type="password" name="password" required>
                        <button name="cek_password">Masuk</button>
                    </form>
                </div>

            <?php } else { ?>

                <h2>Data <?= $_SESSION['nama_pilih'] ?></h2>
                <a href="?logout=1" style="font-weight: bold; font-size: 20px;">🚪 Logout</a>

                <form method="GET" style="display:flex;gap:15px;max-width:400px">
                    <select name="bulan">
                        <?php
                        $bulanNama = [
                            1 => 'Jan',
                            2 => 'Feb',
                            3 => 'Mar',
                            4 => 'Apr',
                            5 => 'Mei',
                            6 => 'Jun',
                            7 => 'Jul',
                            8 => 'Agu',
                            9 => 'Sep',
                            10 => 'Okt',
                            11 => 'Nov',
                            12 => 'Des'
                        ];
                        foreach ($bulanNama as $k => $v) {
                            echo "<option value='$k' " . ($k == $bulan ? 'selected' : '') . ">$v</option>";
                        }
                        ?>
                    </select>

                    <select name="tahun">
                        <?php
                        for ($y = date('Y'); $y >= date('Y') - 5; $y--) {
                            echo "<option " . ($y == $tahun ? 'selected' : '') . ">$y</option>";
                        }
                        ?>
                    </select>

                    <button type="submit">Terapkan</button>
                </form>


                <div style="display: flex ;gap:20px;margin:30px 0">

                    <!-- TOTAL BELANJA PER BULAN -->
                    <div style="
    flex:1;
    background:linear-gradient(135deg, #f8570d , #f85601);
    color:#fff;
    padding:15px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(79,70,229,.4);
">
                        <h4>Total Belanja per Bulan <?= $bulanNama[(int)$bulan] ?> <?= $tahun ?></h4>
                        <h2>Rp <?= number_format($totalBuyBulan) ?></h2>
                    </div>

                    <!-- TOTAL BELANJA PER TAHUN -->
                    <div style="
    flex:1;
    background:linear-gradient(135deg, #ac49fe , #ac38ef);
    color:#fff;
    padding:15px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(22,163,74,.4);
">
                        <h4>Total Belanja per Tahun <?= $tahun ?></h4>
                        <h2>Rp <?= number_format($totalBuyTahun) ?></h2>
                    </div>

                </div>

                <div style="display:flex;gap:20px;margin:30px 0">

                    <!-- TOTAL FEE PER BULAN -->
                    <div style="
    flex:1;
    background:linear-gradient(135deg,#6366f1,#4f46e5);
    color:#fff;
    font-size: 20px;
    padding:25px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(79,70,229,.4);
">
                        <h4>Total Fee per Bulan <?= $bulanNama[(int)$bulan] ?> <?= $tahun ?></h4>
                        <h2>Rp <?= number_format($totalBulan) ?></h2>
                    </div>

                    <!-- TOTAL FEE PER TAHUN -->
                    <div style="
    flex:1;
    background:linear-gradient(135deg,#22c55e,#16a34a);
    color:#fff;
    font-size: 20px;
    padding:25px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(22,163,74,.4);
">
                        <h4>Total Fee per Tahun <?= $tahun ?></h4>
                        <h2>Rp <?= number_format($totalTahun) ?></h2>
                    </div>

                </div>


                <div class="card">
                    <h3>Grafik Fee Bulanan (<?= $tahun ?>)</h3>
                    <div style="height:300px;">
                        <canvas id="feeChart"></canvas>
                    </div>

                </div>

                <div class="card">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Orderan</th>
                                <th>Harga Barang</th>
                                <th>Pembayaran Reseller</th>
                                <th>Sisa Pelunasan</th>
                                <th>Fee (%) Reseller</th>
                                <th>Fee (Rp) Reseller</th>
                                <th>Alamat</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = mysqli_fetch_assoc($data)) {
                                $sisa_pelunasan = $r['harga_barang'] - $r['tagihan_reseller'];
                                $status_lunas  = $sisa_pelunasan == 0 ? 'LUNAS' : 'BELUM LUNAS';

                            ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($r['tanggal'])); ?></td>
                                    <td><?= $r['kode']; ?></td>
                                    <td><?= $r['orderan']; ?></td>
                                    <td class="money">Rp <?= number_format($r['harga_barang']); ?></td>
                                    <td class="money">Rp <?= number_format($r['tagihan_reseller']); ?></td>
                                    <td style="color:red;font-weight:bold">
                                        Rp <?= number_format($sisa_pelunasan); ?>
                                    </td>
                                    <td><span class="badge"><?= $r['fee_persen_reseller']; ?>%</span></td>
                                    <td class="money">Rp <?= number_format($r['fee_rupiah_reseller']); ?></td>
                                    <td>
                                        <button
                                            onclick="toggleAlamat(<?= $r['id']; ?>)"
                                            style="
                                            padding:6px 10px;
                                            border:none;
                                            border-radius:6px;
                                            background:#4f46e5;
                                            color:white;
                                            cursor:pointer;
                                            font-size:12px
                                        ">
                                            👁 Lihat
                                        </button>
                                    </td>
                                    <td>
                                        <?php if ($sisa_pelunasan == 0) { ?>
                                            <span style="color:green;font-weight:bold">LUNAS</span>
                                        <?php } else { ?>
                                            <span style="color:orange;font-weight:bold">BELUM LUNAS</span>
                                        <?php } ?>
                                    </td>

                                <tr id="alamat-<?= $r['id']; ?>" style="display:none">
                                    <td colspan="100%">
                                        <div class="alamat-card">
                                            <div class="alamat-title">
                                                📍 Alamat Pengiriman
                                            </div>
                                            <div class="alamat-text">
                                                <?= nl2br(htmlspecialchars($r['alamat'])); ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <script>
                    new Chart(document.getElementById('feeChart'), {
                        type: 'bar',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                            datasets: [{
                                data: <?= json_encode(array_values($grafik)) ?>,
                                backgroundColor: 'rgba(79,70,229,.85)',
                                borderRadius: 12
                            }]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });

                    function toggleAlamat(id) {
                        const row = document.getElementById('alamat-' + id);
                        row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
                    }
                </script>

            <?php } ?>

        </main>
    </div>

</body>

</html>
