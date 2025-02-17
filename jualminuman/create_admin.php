<?php
include 'config/database.php';

// Data admin
$username = 'admin';
$password = 'admin321'; // password asli
$email = 'admin@example.com';
$role = 'admin';

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Cek apakah admin sudah ada
$check_query = "SELECT * FROM users WHERE username = 'admin'";
$check_result = mysqli_query($conn, $check_query);

if(mysqli_num_rows($check_result) > 0) {
    echo "Admin sudah ada!";
} else {
    // Query untuk insert admin
    $query = "INSERT INTO users (username, password, email, role) 
              VALUES ('$username', '$hashed_password', '$email', '$role')";
    
    if(mysqli_query($conn, $query)) {
        echo "Admin berhasil ditambahkan!<br>";
        echo "Username: admin<br>";
        echo "Password: admin321";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Hapus file ini setelah digunakan
?> 