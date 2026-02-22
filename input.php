<?php
include "koneksi.php";

if (isset($_POST['simpan'])) {
    $tanggal                 = $_POST['tanggal'];
    $nama                    = $_POST['nama'];
    $kode                    = $_POST['kode'];
    $orderan                 = $_POST['orderan'];
    $harga_barang            = $_POST['harga_barang'];
    $fee_persen_reseller     = $_POST['fee_persen_reseller'];
    $fee_rupiah_reseller     = ($harga_barang * $fee_persen_reseller) / 100;
    $fee_persen_owner        = $_POST['fee_persen_owner'];
    $fee_rupiah_owner        = ($harga_barang * $fee_persen_owner) / 100;
    $alamat                  = mysqli_real_escape_string($conn, $_POST['alamat']);
    $keterangan              = $_POST['keterangan'];

    mysqli_query($conn, "INSERT INTO data_orderan_reseller
        (tanggal, nama, kode, orderan, harga_barang, fee_persen_reseller, fee_rupiah_reseller, fee_persen_owner, fee_rupiah_owner, alamat, keterangan)
        VALUES
        ('$tanggal', '$nama', '$kode', '$orderan', '$harga_barang', '$fee_persen_reseller', '$fee_rupiah_reseller', '$fee_persen_owner', '$fee_rupiah_owner', '$alamat', '$keterangan')
    ");

    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Input Order - Saung-Syar'i</title>

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
            font-size: 14px;
            transition: 0.25s;
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

        /* FORM CARD */
        .form-card {
            max-width: 520px;
            background: var(--card);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 14px;
            transition: 0.2s;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        textarea {
            resize: vertical;
        }

        /* BUTTON */
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.25s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(22, 163, 74, 0.45);
        }
    </style>

</head>

<body>

    <div class="app">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <h1 class="logo">Saung-Syar'i</h1>
            <nav>
                <a href="dashboard.php" class="active">📊 Dashboard</a>
                <a href="admin.php">📦 Order</a>
                <a href="data-reseller.php">🛒 Reseller</a>
                <a href="owner.php" class="sidebar-link">👑 Owner</a>
            </nav>
        </aside>

        <!-- CONTENT -->
        <main class="content">
            <h2>Input Order Reseller</h2>

            <div class="form-card">
                <form method="POST">

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" required>
                    </div>

                    <div class="form-group">
                        <label>Nama</label>
                        <select name="nama" required>
                            <option value=""></option>
                            <option value="ETY">ETY KURNIAWAN</option>
                            <option value="SISWATI">SISWATI</option>
                            <option value="MUJIYATI">MUJIYATI</option>
                            <option value="LATIFAH">LATIFAH</option>
                            <option value="UMI">UMI FADILLAH</option>
                            <option value="IIS_H">IIS HERLIANA</option>
                            <option value="SRI">SRI ARYANTI</option>
                            <option value="AINUN">AINUN ALFI</option>
                            <option value="FERA">FERA FERDIANA</option>
                            <option value="IIS_S">IIS SITI NUR A</option>
                            <option value="MUTHOHAROH">MUTHOHAROH</option>
                            <option value="NURUL">NURUL</option>
                            <option value="ANEU">ANEU NURUL</option>
                            <option value="HELDA">HELDA ASTRI</option>
                            <option value="SAUNG">SAUNG SYARI</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Kode</label>
                        <input type="text" name="kode" required>
                    </div>

                    <div class="form-group">
                        <label>Orderan</label>
                        <input type="text" name="orderan" required>
                    </div>

                    <div class="form-group">
                        <label>Harga Barang</label>
                        <input type="number" name="harga_barang" required>
                    </div>

                    <div class="form-group">
                        <label>Fee (%) Reseller</label>
                        <input type="number" name="fee_persen_reseller" required>
                    </div>

                    <div class="form-group">
                        <label>Fee (%) Owner</label>
                        <input type="number" name="fee_persen_owner" required>
                    </div>

                    <div style="margin-bottom:12px">
                        <label>Alamat</label>
                        <textarea
                            name="alamat"
                            rows="3"
                            placeholder="Masukkan alamat lengkap"
                            style="width:100%;padding:10px;border-radius:8px"></textarea>
                    </div>


                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan"></textarea>
                    </div>

                    <button type="submit" name="simpan" class="btn-submit">
                        💾 Simpan Order
                    </button>

                </form>
            </div>
        </main>

    </div>

</body>

</html>
