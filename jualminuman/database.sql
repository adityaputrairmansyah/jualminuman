CREATE DATABASE db_jualminuman;
USE db_jualminuman;

CREATE TABLE kategori (
    id_kategori INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(50) NOT NULL
);

CREATE TABLE produk (
    id_produk INT PRIMARY KEY AUTO_INCREMENT,
    nama_produk VARCHAR(100) NOT NULL,
    id_kategori INT,
    harga DECIMAL(10,2) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255),
    stok INT NOT NULL,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
);

CREATE TABLE users (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    nama_lengkap VARCHAR(100) NULL,
    alamat TEXT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS detail_pesanan;
DROP TABLE IF EXISTS pesanan;

CREATE TABLE pesanan (
    id_pesanan INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT,
    tanggal_pesanan DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'dibayar', 'dikirim', 'selesai') DEFAULT 'pending',
    total_harga DECIMAL(10,2),
    metode_pembayaran VARCHAR(50) NULL,
    alamat_pengiriman TEXT NULL,
    tanggal_pembayaran DATETIME NULL,
    catatan TEXT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);

CREATE TABLE detail_pesanan (
    id_detail INT PRIMARY KEY AUTO_INCREMENT,
    id_pesanan INT,
    id_produk INT,
    jumlah INT,
    harga DECIMAL(10,2),
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan),
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk)
);

CREATE TABLE pesan_kontak (
    id_pesan INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subjek VARCHAR(200) NOT NULL,
    pesan TEXT NOT NULL,
    tanggal DATETIME NOT NULL,
    status ENUM('baru', 'dibaca', 'dibalas') DEFAULT 'baru'
);

ALTER TABLE users 
MODIFY nama_lengkap VARCHAR(100) NULL,
MODIFY alamat TEXT NULL;

ALTER TABLE pesanan 
ADD COLUMN metode_pembayaran VARCHAR(50) NULL AFTER total_harga,
ADD COLUMN alamat_pengiriman TEXT NULL AFTER metode_pembayaran,
ADD COLUMN tanggal_pembayaran DATETIME NULL AFTER alamat_pengiriman,
ADD COLUMN catatan TEXT NULL AFTER tanggal_pembayaran; 