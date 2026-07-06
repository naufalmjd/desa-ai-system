<?php
$hash = '$2y$12$92lXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2...'; // ganti dengan hash LENGKAP dari kolom password di phpMyAdmin (klik Edit biar keliatan full)
$password = 'password123';

var_dump(password_verify($password, $hash));