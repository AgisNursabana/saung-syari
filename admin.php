<?php
session_start();

/* =========================
   PASSWORD ADMIN
========================= */
$password_admin = "owner1111"; // ← GANTI PASSWORD DI SINI


/* =========================
   LOGOUT
========================= */
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}


/* =========================
   CEK LOGIN
========================= */
if (!isset($_SESSION['admin_login'])) {

    if (isset($_POST['password'])) {
        if ($_POST['password'] === $password_admin) {
            $_SESSION['admin_login'] = true;
            header("Location: admin.php");
            exit;
        } else {
            $error = "Password Salah!";
        }
    }
    ?>

    <!-- ================= LOGIN FORM ================= -->
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login Admin</title>
        <style>
            body{
                font-family:Segoe UI;
                background:#f4f6fb;
                display:flex;
                justify-content:center;
                align-items:center;
                height:100vh;
            }
            .box{
                background:white;
                padding:40px;
                border-radius:12px;
                box-shadow:0 10px 25px rgba(0,0,0,.1);
                width:300px;
                text-align:center;
            }
            input{
                width:100%;
                padding:10px;
                margin:10px 0;
                border-radius:8px;
                border:1px solid #ddd;
            }
            button{
                padding:10px;
                width:100%;
                border:none;
                border-radius:8px;
                background:#4f46e5;
                color:white;
                font-weight:bold;
                cursor:pointer;
            }
            .error{color:red;font-size:14px;}
        </style>
    </head>

    <body>
        <form method="POST" class="box">
            <h3>🔐 Login Admin</h3>

            <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

            <input type="password" name="password" placeholder="Masukkan Password" required>
            <button type="submit">MASUK</button>
        </form>
    </body>
    </html>

    <?php
    exit; // ⛔ hentikan semua kode jika belum login
}
/* =========================
   JIKA SUDAH LOGIN → LANJUTKAN HALAMAN
========================= */
?>

<?php
include "koneksi.php";

/* =========================
   FILTER DATA
========================= */
$filterNama = $_GET['nama'] ?? '';
$filterKode = $_GET['kode'] ?? '';

$where = [];

if ($filterKode !== '') {
    $kodeEsc = mysqli_real_escape_string($conn, $filterKode);
    $where[] = "kode LIKE '%$kodeEsc%'";
}

if ($filterNama !== '') {
    $namaEsc = mysqli_real_escape_string($conn, $filterNama);
    $where[] = "nama = '$namaEsc'";
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$data = mysqli_query($conn, "
    SELECT *
    FROM data_orderan_reseller
    $whereSQL
    ORDER BY tanggal DESC, id DESC
");


/* =========================
   UPDATE TAGIHAN
========================= */
if (isset($_POST['id_tagihan'])) {
    $id      = (int)$_POST['id_tagihan'];
    $tagihan = (int)$_POST['tagihan'];

    mysqli_query($conn, "
        UPDATE data_orderan_reseller
        SET tagihan_reseller = $tagihan
        WHERE id = $id
    ");

    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Data Orderan Saung-Syar'i</title>
    <style>
        :root {
            --primary: #4f46e5;
            --bg: #f4f6fb;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #e5e7eb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Inter, system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .header-table {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.35);
            transition: all 0.25s ease;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(22, 163, 74, 0.45);
        }


        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #111827, #1f2937);
            color: #fff;
            padding: 30px 20px;
        }

        .logo {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 40px;
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

        .sidebar nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .sidebar nav a.active {
            background: var(--primary);
            color: #fff;
        }

        /* CONTENT */
        .content {
            flex: 1;
            padding: 40px;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 26px;
            font-weight: 600;
        }

        .table-container {
            background: var(--card);
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, var(--primary), #6366f1);
            color: #fff;
        }

        th {
            padding: 14px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 14px;
            font-size: 18px;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }

        tbody tr {
            transition: 0.25s ease;
        }

        tbody tr:hover {
            background: #eef2ff;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            background: #e0e7ff;
            color: var(--primary);
            font-weight: 600;
        }

        .money {
            color: #16a34a;
            font-weight: 600;
        }

        .logo-saung {
            height: 145px;
            /* atur tinggi logo */
            width: auto;
            object-fit: contain;
            border-radius: 20px;
            margin-bottom: 50px;
        }

        .alamat-card {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 18px;
            margin: 8px 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .04);
        }

        .alamat-title {
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .alamat-text {
            font-size: 14px;
            line-height: 1.6;
            color: #374151;
        }



        /* RESPONSIVE */
        @media (max-width: 900px) {
            .sidebar {
                width: 200px;
            }

            .content {
                padding: 20px;
            }
        }
    </style>


</head>

<body>

    <div class="app">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <img
                src="foto/saungSyari.png"
                alt="Logo Saung Syar'i"
                class="logo-saung" />

            <nav>
                <a href="dashboard.php">📊 Dashboard</a>
                <a href="admin.php" class="active">📦 Order</a>
                <a href="data-reseller.php">🛒 Reseller</a>
                <a href="owner.php" class="sidebar-link">👑 Owner</a>
            </nav>
        </aside>

        <!-- CONTENT -->
        <main class="content">
            <div class="header-table">
                <h2>Data Orderan Saung-Syar'i</h2>
            </div>

            <div style="
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:20px;
    gap:16px;
">

                <!-- FILTER KIRI -->
                <form method="GET" style="display:flex;gap:12px;align-items:center">

                    <!-- FILTER NAMA -->
                    <select
                        name="nama"
                        style=" width:200px;padding:10px;border-radius:8px">
                        <option value="">— Semua Reseller —</option>
                        <?php
                        $qNama = mysqli_query($conn, "
                SELECT DISTINCT nama
                FROM data_orderan_reseller
                ORDER BY nama
            ");
                        while ($n = mysqli_fetch_assoc($qNama)) {
                            $selected = ($filterNama == $n['nama']) ? 'selected' : '';
                            echo "<option value='{$n['nama']}' $selected>{$n['nama']}</option>";
                        }
                        ?>
                    </select>

                    <!-- SEARCH BY KODE -->
                    <input
                        type="text"
                        name="kode"
                        value="<?= htmlspecialchars($filterKode); ?>"
                        placeholder="Cari Kode (A1, SS1)"
                        style="
                padding:10px;
                width:140px;
                border-radius:8px;
                border:1px solid #ddd;
                text-transform:uppercase;
            ">

                    <button type="submit" style="
            padding:10px 16px;
            border:none;
            border-radius:8px;
            background:#4f46e5;
            color:#fff;
            font-weight:600;
            cursor:pointer;
        ">
                        Terapkan
                    </button>

                    <!-- RESET -->
                    <a href="admin.php" style="
            padding:10px 14px;
            border-radius:8px;
            background:#e5e7eb;
            text-decoration:none;
            color:#111;
            font-weight:600;
        ">
                        Reset
                    </a>

                </form>

                <!-- BUTTON KANAN -->
                <a href="input.php" class="btn-add">
                    ➕ Input Order
                </a>

            </div>






            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Orderan</th>
                            <th>Harga Barang</th>
                            <th>DP (Down Payment)</th>
                            <th>Sisa Pelunasan</th>
                            <th>Fee (%) Reseller</th>
                            <th>Fee (Rp) Reseller</th>
                            <th>Fee (%) Owner</th>
                            <th>Fee (Rp) Owner</th>
                            <th>Alamat</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($data)) {
                            $sisa_pelunasan = $row['harga_barang'] - $row['tagihan_reseller'];
                            $status_lunas  = $sisa_pelunasan == 0 ? 'LUNAS' : 'BELUM LUNAS';

                        ?>
                            <tr>

                                <td><?= $row['id']; ?></td>
                                <td><?= date('d M Y', strtotime($row['tanggal'])); ?></td>
                                <td><?= $row['nama']; ?></td>
                                <td><?= $row['kode']; ?></td>
                                <td><?= $row['orderan']; ?></td>
                                <td class="money">Rp <?= number_format($row['harga_barang']); ?></td>
                                <td>
                                    <form method="POST" style="display:flex;gap:5px;text-align:center;">
                                        <input type="text"
                                            name="tagihan_display"
                                            value="<?= number_format($row['tagihan_reseller'], 0, ',', '.'); ?>"
                                            oninput="formatRupiah(this)"
                                            style="width:120px;padding:4px;margin:10px;">

                                        <input type="hidden"
                                            name="tagihan"
                                            value="<?= $row['tagihan_reseller']; ?>">

                                        <input type="hidden"
                                            name="id_tagihan"
                                            value="<?= $row['id']; ?>">

                                        <button style="
                                        background-color: #04ef00;
                                        color:#fff;
                                        border:none;
                                        border-radius:6px;
                                        padding:10px;
                                        cursor:pointer;
                                        ">💾</button>
                                    </form>

                                </td>
                                <td style="color:red;font-weight:bold">
                                    Rp <?= number_format($sisa_pelunasan); ?>
                                </td>


                                <td><span class="badge"><?= $row['fee_persen_reseller']; ?>%</span></td>
                                <td class="money">Rp <?= number_format($row['fee_rupiah_reseller']); ?></td>
                                <td><span class="badge"><?= $row['fee_persen_owner']; ?>%</span></td>
                                <td class="money">Rp <?= number_format($row['fee_rupiah_owner']); ?></td>
                                <td>
                                    <button
                                        onclick="toggleAlamat(<?= $row['id']; ?>)"
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

                            <tr id="alamat-<?= $row['id']; ?>" style="display:none">
                                <td colspan="100%">
                                    <div class="alamat-card">
                                        <div class="alamat-title">
                                            📍 Alamat Pengiriman
                                        </div>
                                        <div class="alamat-text">
                                            <?= nl2br(htmlspecialchars($row['alamat'])); ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>




                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        function formatRupiah(el) {
            let angka = el.value.replace(/[^0-9]/g, '');
            el.nextElementSibling.value = angka;

            el.value = new Intl.NumberFormat('id-ID').format(angka);
        }


        function toggleAlamat(id) {
            const row = document.getElementById('alamat-' + id);
            row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
        }
    </script>


</body>

</html>
