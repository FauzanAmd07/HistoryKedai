<?php
session_start();

// Cek apakah session 'status' tidak ada atau tidak bernilai "login"
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    // Jika tidak, alihkan pengguna ke halaman login dengan pesan
    header("location:login.php?pesan=belum_login");
    exit; // Pastikan skrip berhenti setelah redirect
}
?>