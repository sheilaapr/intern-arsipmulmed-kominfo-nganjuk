<?php
require_once __DIR__.'/auth.php';
if(is_logged_in())redirect('dashboard.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); $nama=trim((string)($_POST['nama']??'')); $email=strtolower(trim((string)($_POST['email']??'')));
 $username=trim((string)($_POST['username']??'')); $password=(string)($_POST['password']??''); $confirm=(string)($_POST['confirm_password']??'');
 if($nama===''||$email===''||$username===''||$password===''||$confirm==='')$error='Semua kolom wajib diisi.';
 elseif(text_length($nama)<3||text_length($nama)>50)$error='Nama harus berisi 3–50 karakter.';
 elseif(!filter_var($email,FILTER_VALIDATE_EMAIL))$error='Format email tidak valid.';
 elseif(!preg_match('/^[a-zA-Z0-9._-]{3,50}$/',$username))$error='Username hanya boleh berisi huruf, angka, titik, garis bawah, atau minus.';
 elseif(strlen($password)<8)$error='Password minimal 8 karakter.';
 elseif($password!==$confirm)$error='Konfirmasi password tidak sama.';
 else{
  $q=$koneksi->prepare('SELECT id FROM users WHERE username=? OR email=? LIMIT 1');$q->bind_param('ss',$username,$email);$q->execute();
  if($q->get_result()->num_rows)$error='Username atau email sudah digunakan.';
  else{$role='staff';$hash=password_hash($password,PASSWORD_DEFAULT);$q=$koneksi->prepare('INSERT INTO users(nama,email,role,username,password,created) VALUES(?,?,?,?,?,NOW())');$q->bind_param('sssss',$nama,$email,$role,$username,$hash);
   if($q->execute()){set_flash('success','Akun berhasil dibuat. Silakan masuk.');redirect('index.php');} $error='Akun gagal dibuat.';}
 }
} ?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Daftar · Omah Tandang</title><link rel="icon" href="../assets/img/favicon.png"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><link rel="stylesheet" href="../assets/css/app.css?v=2.0.0"></head><body>
<main class="login-page"><section class="auth-panel"><div class="auth-box"><a class="auth-brand" href="index.php"><span class="brand-mark"><i class="fa-solid fa-house-chimney-window"></i></span><span><strong>Omah Tandang</strong><span>Arsip Multimedia</span></span></a><span class="page-heading"><span>Akun baru</span></span><h1>Daftar sebagai staf</h1><p>Buat akun agar dapat menambahkan dan mengelola dokumentasi kegiatan.</p>
<?php if($error):?><div class="auth-alert"><?=e($error)?></div><?php endif;?>
<form method="post" class="auth-form" data-submit-lock><?=csrf_field()?>
<div class="field"><label>Nama lengkap</label><input class="input" name="nama" value="<?=e($_POST['nama']??'')?>" maxlength="50" required></div>
<div class="field"><label>Email</label><input class="input" name="email" type="email" value="<?=e($_POST['email']??'')?>" maxlength="100" required></div>
<div class="field"><label>Username</label><input class="input" name="username" value="<?=e($_POST['username']??'')?>" pattern="[A-Za-z0-9._-]{3,50}" maxlength="50" required></div>
<div class="form-grid"><div class="field"><label>Password</label><div class="input-wrap"><input class="input" id="signupPassword" name="password" type="password" minlength="8" required><button class="icon-btn input-action" type="button" data-password-toggle="signupPassword"><i class="fa-regular fa-eye"></i></button></div></div><div class="field"><label>Konfirmasi</label><div class="input-wrap"><input class="input" id="confirmPassword" name="confirm_password" type="password" minlength="8" required><button class="icon-btn input-action" type="button" data-password-toggle="confirmPassword"><i class="fa-regular fa-eye"></i></button></div></div></div>
<button class="btn btn-primary w-full" type="submit"><i class="fa-solid fa-user-plus"></i> Buat akun</button></form><p class="auth-note">Sudah memiliki akun? <a href="index.php" style="color:#3157d5;font-weight:800">Kembali masuk</a></p></div></section>
<aside class="auth-art"><div class="auth-art-copy"><span class="hero-kicker">Satu ruang kerja</span><h2>Kolaborasi dokumentasi yang lebih teratur.</h2><p>Setiap staf dapat menambahkan dokumentasi, sementara Kepala Bagian mengontrol anggota dan struktur data.</p></div></aside></main><script src="../assets/js/app.js?v=2.0.0"></script></body></html>