CREATE DATABASE relantara
    DEFAULT CHARACTER SET = 'utf8mb4';

use relantara;

-- Start

CREATE TABLE tbl_admin (
  id_admin INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(150)
) ENGINE=InnoDB;

CREATE TABLE tbl_volunteer (
  id_volunteer INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(255) NOT NULL,
  penyelenggara VARCHAR(150),
  deskripsi TEXT,
  lokasi VARCHAR(200),
  tanggal_mulai DATE,
  tanggal_selesai DATE,
  link_pendaftaran VARCHAR(255),
  gambar_poster VARCHAR(255),
  tanggal_posting TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


ALTER TABLE `tbl_volunteer`
    ADD `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ADD `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL;


ALTER TABLE `tbl_admin`
    ADD `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ADD `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL;

