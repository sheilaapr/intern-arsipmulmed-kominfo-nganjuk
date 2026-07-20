<?php
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$database = getenv('DB_NAME') ?: 'omahtandang';
$port = (int)(getenv('DB_PORT') ?: 3306);

mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = mysqli_connect($host, $username, $password, $database, $port);
if (!$koneksi) {
    http_response_code(500);
    exit('Koneksi database gagal. Periksa pengaturan database.');
}
mysqli_set_charset($koneksi, 'utf8mb4');

mysqli_query($koneksi, "
CREATE TABLE IF NOT EXISTS arsip_file (
 id_file INT NOT NULL AUTO_INCREMENT,
 id_arsip INT NOT NULL,
 filename VARCHAR(255) NOT NULL,
 tipe_file VARCHAR(20) NOT NULL,
 created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (id_file),
 KEY idx_arsip_file_id_arsip (id_arsip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
// Migrasi kecil hanya dijalankan sekali pada database lama.
mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS app_meta (
  meta_key VARCHAR(100) NOT NULL PRIMARY KEY,
  meta_value VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
$versionResult = mysqli_query($koneksi, "SELECT meta_value FROM app_meta WHERE meta_key='schema_version' LIMIT 1");
$schemaVersion = $versionResult && mysqli_num_rows($versionResult) ? (string)mysqli_fetch_assoc($versionResult)['meta_value'] : '1';
if (version_compare($schemaVersion, '2.0', '<')) {
    @mysqli_query($koneksi, "ALTER TABLE kategori MODIFY deskripsi VARCHAR(500) NULL");
    @mysqli_query($koneksi, "ALTER TABLE users MODIFY email VARCHAR(100) NOT NULL");
    @mysqli_query($koneksi, "ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL");
    @mysqli_query($koneksi, "ALTER TABLE users MODIFY created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
    @mysqli_query($koneksi, "ALTER TABLE arsip MODIFY created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
    mysqli_query($koneksi, "INSERT INTO app_meta(meta_key,meta_value) VALUES('schema_version','2.0')
      ON DUPLICATE KEY UPDATE meta_value='2.0'");
}
?>