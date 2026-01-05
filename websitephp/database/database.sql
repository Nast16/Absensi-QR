-- ================================
--  DATABASE MERGE-READY
--  Nama Database: db_kampus
--  Struktur aman & siap digabung
-- ================================

-- Jika database sudah ada, hapus tanda komentar pada baris berikut:
-- DROP DATABASE IF EXISTS db_kampus;

CREATE DATABASE IF NOT EXISTS db_kampus
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE db_kampus;

-- ================================
--  TABLE: users
--  Perbaikan:
--   ✔ Ditambah IF NOT EXISTS
--   ✔ Field distandarisasi
--   ✔ Penamaan konsisten
--   ✔ ENUM aman untuk merge
--   ✔ UNIQUE pada username
-- ================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    status ENUM('Mahasiswa','Dosen') NOT NULL,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    jenis_kelamin ENUM('L','P') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- ================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    status ENUM('dosen','mahasiswa'),
    username VARCHAR(50),
    password VARCHAR(255)
);


-- ================================
--  Area Insert Data (Opsional)
--  Kosong agar tidak konflik saat merge
-- ================================
-- INSERT INTO users (nama, status, username, password, jenis_kelamin)
-- VALUES ('Contoh Nama', 'Mahasiswa', 'contoh', '12345', 'L');
