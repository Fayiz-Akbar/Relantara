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

CREATE TABLE tbl_kategori (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE tbl_kegiatan (
  id_kegiatan INT AUTO_INCREMENT PRIMARY KEY,
  id_penyelenggara INT NOT NULL,
  id_kategori INT,
  judul VARCHAR(255) NOT NULL,
  deskripsi TEXT NOT NULL,
  lokasi VARCHAR(200),
  tanggal_mulai DATE,
  tanggal_selesai DATE,
  gambar_poster VARCHAR(255),
  kuota INT DEFAULT 0,
  benefit TEXT,
  status_kegiatan ENUM('Draft', 'Published', 'Completed', 'Cancelled') DEFAULT 'Published',
  tanggal_posting TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_penyelenggara) REFERENCES tbl_penyelenggara(id_penyelenggara) ON DELETE CASCADE,
  FOREIGN KEY (id_kategori) REFERENCES tbl_kategori(id_kategori) ON DELETE SET NULL
) ENGINE=InnoDB;

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

INSERT INTO tbl_admin (username, password, nama_lengkap) 
VALUES ('admin', '$2y$10$Y.aJ3u.3y.E/ii.A8kI.De.L.B.rJ1O6bY8z.N3fJ5.b.mXzJ3j.K', 'Admin Utama');

INSERT INTO tbl_kategori (nama_kategori) 
VALUES ('Pendidikan'), ('Lingkungan'), ('Kesehatan'), ('Sosial'), ('Bencana Alam');
