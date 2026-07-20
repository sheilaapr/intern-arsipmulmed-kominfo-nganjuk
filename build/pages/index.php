<?php
require_once __DIR__.'/auth.php';
if(is_logged_in())redirect('dashboard.php');
$error=''; $flash=get_flash();
if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf(); $identity=trim((string)($_POST['identity']??'')); $password=(string)($_POST['password']??'');
  if($identity===''||$password==='')$error='Username/email dan password wajib diisi.';
  else{
    $stmt=$koneksi->prepare('SELECT id,nama,email,role,username,password,foto_profile FROM users WHERE username=? OR email=? LIMIT 1');
    $stmt->bind_param('ss',$identity,$identity); $stmt->execute(); $u=$stmt->get_result()->fetch_assoc();
    if(!$u||!password_verify($password,$u['password']))$error='Username/email atau password tidak sesuai.';
    else{
      session_regenerate_id(true);
      $_SESSION['id']=(int)$u['id']; $_SESSION['nama']=$u['nama']; $_SESSION['email']=$u['email']; $_SESSION['role']=$u['role'];
      $_SESSION['username']=$u['username']; $_SESSION['foto_profile']=$u['foto_profile'];
      set_flash('success','Selamat datang kembali, '.$u['nama'].'!'); redirect('dashboard.php');
    }
  }
} ?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Masuk · Omah Tandang</title><link rel="icon" href="../assets/img/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><link rel="stylesheet" href="../assets/css/app.css?v=2.0.0"></head><body>
<main class="login-page"><section class="auth-panel"><div class="auth-box">
<a class="auth-brand" href="index.php"><span class="brand-mark"><i class="fa-solid fa-house-chimney-window"></i></span><span><strong>Omah Tandang</strong><span>Arsip Multimedia</span></span></a>
<span class="page-heading"><span>Selamat datang</span></span><h1>Masuk ke akun Anda</h1><p>Kelola kategori, dokumentasi kegiatan, dan anggota dalam satu sistem yang rapi dan mudah digunakan.</p>
<?php if($flash):?><div class="auth-alert" style="background:#eef7ff;color:#3157d5"><?=e($flash['message'])?></div><?php endif;?>
<?php if($error):?><div class="auth-alert"><?=e($error)?></div><?php endif;?>
<form method="post" class="auth-form" data-submit-lock><?=csrf_field()?>
<div class="field"><label for="identity">Username atau email</label><input class="input" id="identity" name="identity" value="<?=e($_POST['identity']??'')?>" required autofocus autocomplete="username"></div>
<div class="field"><label for="loginPassword">Password</label><div class="input-wrap"><input class="input" id="loginPassword" name="password" type="password" required autocomplete="current-password"><button class="icon-btn input-action" type="button" data-password-toggle="loginPassword"><i class="fa-regular fa-eye"></i></button></div></div>
<button class="btn btn-primary w-full" type="submit"><i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk</button></form>
<p class="auth-note">Belum punya akun? <a href="signup.php" style="color:#3157d5;font-weight:800">Daftar sebagai staf</a></p>
</div></section><aside class="auth-art"><div class="auth-art-copy"><span class="hero-kicker">Rumah Talenta Digital Anjuk Ladang</span><h2>Dokumentasi lebih rapi, pencarian lebih cepat.</h2><p>Simpan foto kegiatan dan dokumen penting berdasarkan kategori agar seluruh tim mudah menemukan arsip yang dibutuhkan.</p></div></aside></main>
<script src="../assets/js/app.js?v=2.0.0"></script></body></html>