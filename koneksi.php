<?php
$conn = mysqli_connect("localhost", "root", "", "saung_syari_official");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
