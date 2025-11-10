USE relantara;

CREATE TABLE tbl_admin (
  id_admin INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(150)
) ENGINE=InnoDB;

CREATE TABLE tbl_penyelenggara (
  id_penyelenggara INT AUTO_INCREMENT PRIMARY KEY,
  nama_organisasi VARCHAR(200) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  deskripsi TEXT,
  logo VARCHAR(255) DEFAULT 'default_logo.png',
  alamat TEXT,
  kontak_email VARCHAR(150),
  kontak_telp VARCHAR(20),
  status_verifikasi ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
  tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE tbl_relawan (
  id_relawan INT AUTO_INCREMENT PRIMARY KEY,
  nama_lengkap VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  foto_profil VARCHAR(255) DEFAULT 'default_profil.png',
  bio TEXT,
  keahlian TEXT,
  tanggal_lahir DATE,
  jenis_kelamin ENUM('Pria', 'Wanita', 'Lainnya'),
  tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE tbl_kegiatan (
  id_kegiatan INT AUTO_INCREMENT PRIMARY KEY,

  id_penyelenggara INT NOT NULL, 


  judul VARCHAR(255) NOT NULL,
  deskripsi TEXT NOT NULL,
  lokasi VARCHAR(200),
  tanggal_mulai DATE,
  tanggal_selesai DATE,
  gambar_poster VARCHAR(255),
  kuota INT DEFAULT 0 COMMENT '0 berarti tidak terbatas',
  benefit TEXT COMMENT 'Contoh: Sertifikat, Konsumsi, Transport, Relasi',
  status_kegiatan ENUM('Draft', 'Published', 'Completed', 'Cancelled') DEFAULT 'Published',
  tanggal_posting TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (id_penyelenggara) REFERENCES tbl_penyelenggara(id_penyelenggara) ON DELETE CASCADE
) ENGINE=InnoDB;


ALTER TABLE `tbl_admin`
    ADD `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ADD `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE `tbl_kegiatan`
    ADD `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ADD `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    MODIFY `status_kegiatan` ENUM('Pending', 'Published', 'Rejected', 'Completed', 'Cancelled') 
        NOT NULL DEFAULT 'Pending';

ALTER TABLE `tbl_penyelenggara`
    ADD `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `tanggal_daftar`,
    ADD `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE `tbl_relawan`
    ADD `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `tanggal_daftar`,
    ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL;

CREATE TABLE tbl_pendaftaran (
  id_pendaftaran INT AUTO_INCREMENT PRIMARY KEY,
  id_relawan INT NOT NULL,
  id_kegiatan INT NOT NULL,
  tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status_pendaftaran ENUM('Pending', 'Diterima', 'Ditolak', 'Selesai') DEFAULT 'Pending',
  alasan_bergabung TEXT,
  UNIQUE KEY (id_relawan, id_kegiatan), 
  FOREIGN KEY (id_relawan) REFERENCES tbl_relawan(id_relawan) ON DELETE CASCADE,
  FOREIGN KEY (id_kegiatan) REFERENCES tbl_kegiatan(id_kegiatan) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tbl_kategori (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL UNIQUE,
  deskripsi TEXT,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Untuk soft delete'
) ENGINE=InnoDB;

CREATE TABLE tbl_kegiatan_kategori (
  id_kegiatan INT NOT NULL,
  id_kategori INT NOT NULL,
  PRIMARY KEY (id_kegiatan, id_kategori), 
  
  FOREIGN KEY (id_kegiatan) REFERENCES tbl_kegiatan(id_kegiatan) ON DELETE CASCADE,
  FOREIGN KEY (id_kategori) REFERENCES tbl_kategori(id_kategori) ON DELETE CASCADE
) ENGINE=InnoDB;





-- Dummy Data for Testing
INSERT INTO `tbl_penyelenggara` 
    (`id_penyelenggara`, `nama_organisasi`, `email`, `password`, `status_verifikasi`) 
VALUES 
    (1, 'Komunitas Peduli Lingkungan', 'komunitas@email.com', '$2y$10$Y.aJ3u.3y.E/ii.A8kI.De.L.B.rJ1O6bY8z.N3fJ5.b.mXzJ3j.K', 'Verified');

INSERT INTO `tbl_kegiatan` 
    (`id_penyelenggara`, `judul`, `deskripsi`, `lokasi`, `benefit`) 
VALUES 
    (1, 'Dummy: Tanam 1000 Pohon', 'Deskripsi untuk kegiatan tanam pohon...', 'Hutan Kota', 'Sertifikat, Makan Siang'),
    (1, 'Dummy: Ajar Koding Gratis', 'Deskripsi untuk kegiatan ajar koding...', 'Online via Zoom', 'Relasi, Portofolio');
