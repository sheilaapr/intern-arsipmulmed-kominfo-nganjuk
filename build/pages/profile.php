<?php
require_once __DIR__.'/auth.php';
require_once __DIR__.'/partials/layout.php';
require_login();
$userId=(int)$_SESSION['id'];

if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$action=(string)($_POST['action']??'');
 if($action==='update_profile'){
   $nama=trim((string)($_POST['nama']??''));$email=strtolower(trim((string)($_POST['email']??'')));$username=trim((string)($_POST['username']??''));
   if($nama===''||text_length($nama)>50||!filter_var($email,FILTER_VALIDATE_EMAIL)||!preg_match('/^[a-zA-Z0-9._-]{3,50}$/',$username)){set_flash('danger','Data profil belum valid.');redirect('profile.php');}
   $q=$koneksi->prepare('SELECT id FROM users WHERE (email=? OR username=?) AND id<>? LIMIT 1');$q->bind_param('ssi',$email,$username,$userId);$q->execute();
   if($q->get_result()->num_rows){set_flash('warning','Email atau username sudah digunakan akun lain.');redirect('profile.php');}
   $q=$koneksi->prepare('SELECT foto_profile FROM users WHERE id=?');$q->bind_param('i',$userId);$q->execute();$old=$q->get_result()->fetch_assoc()['foto_profile']??null;$newFile=$old;
   if(isset($_FILES['foto_profile'])&&($_FILES['foto_profile']['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_NO_FILE){
     $file=$_FILES['foto_profile'];if((int)$file['error']!==UPLOAD_ERR_OK||(int)$file['size']<=0||(int)$file['size']>3*1024*1024){set_flash('danger','Foto profil maksimal 3 MB.');redirect('profile.php');}
     $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));$mime=class_exists('finfo')?(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']):$file['type'];
     $valid=['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp'];
     if(!isset($valid[$ext])||$valid[$ext]!==$mime||!is_uploaded_file($file['tmp_name'])){set_flash('danger','Foto harus berformat JPG, PNG, atau WEBP.');redirect('profile.php');}
     $newFile='profile_'.$userId.'_'.bin2hex(random_bytes(6)).'.'.$ext;$dir=__DIR__.'/uploads/profile';if(!is_dir($dir))mkdir($dir,0775,true);
     if(!move_uploaded_file($file['tmp_name'],$dir.'/'.$newFile)){set_flash('danger','Foto gagal disimpan.');redirect('profile.php');}
   }
   $q=$koneksi->prepare('UPDATE users SET nama=?,email=?,username=?,foto_profile=? WHERE id=?');$q->bind_param('ssssi',$nama,$email,$username,$newFile,$userId);$ok=$q->execute();
   if($ok){if($newFile!==$old&&$old)delete_uploaded_file('profile/'.$old);$_SESSION['nama']=$nama;$_SESSION['email']=$email;$_SESSION['username']=$username;$_SESSION['foto_profile']=$newFile;set_flash('success','Profil berhasil diperbarui.');}
   else{if($newFile!==$old)delete_uploaded_file('profile/'.$newFile);set_flash('danger','Profil gagal diperbarui.');}
   redirect('profile.php');
 }
 if($action==='change_password'){
   $current=(string)($_POST['current_password']??'');$new=(string)($_POST['new_password']??'');$confirm=(string)($_POST['confirm_password']??'');
   $q=$koneksi->prepare('SELECT password FROM users WHERE id=?');$q->bind_param('i',$userId);$q->execute();$hash=$q->get_result()->fetch_assoc()['password']??'';
   if(!password_verify($current,$hash))set_flash('danger','Password saat ini tidak sesuai.');
   elseif(strlen($new)<8)set_flash('warning','Password baru minimal 8 karakter.');
   elseif($new!==$confirm)set_flash('warning','Konfirmasi password baru tidak sama.');
   else{$newHash=password_hash($new,PASSWORD_DEFAULT);$q=$koneksi->prepare('UPDATE users SET password=? WHERE id=?');$q->bind_param('si',$newHash,$userId);$ok=$q->execute();set_flash($ok?'success':'danger',$ok?'Password berhasil diganti.':'Password gagal diganti.');}
   redirect('profile.php');
 }
}
$q=$koneksi->prepare('SELECT id,nama,email,role,username,created,foto_profile FROM users WHERE id=?');$q->bind_param('i',$userId);$q->execute();$user=$q->get_result()->fetch_assoc();if(!$user){$_SESSION=[];session_destroy();redirect('index.php');}
$archiveCount=0;$q=$koneksi->prepare('SELECT COUNT(*) total FROM arsip WHERE uploaded_by=?');$q->bind_param('i',$userId);$q->execute();$archiveCount=(int)$q->get_result()->fetch_assoc()['total'];
$avatar=avatar_src($user['foto_profile']);
render_app_start('Profil Saya','Perbarui identitas akun dan keamanan password.','profile.php'); ?>
<section class="profile-hero"><span class="avatar avatar-lg"><?php if($avatar):?><img src="<?=e($avatar)?>" alt="Foto profil"><?php else:?><?=e(initials($user['nama']))?><?php endif;?></span><div><span class="hero-kicker"><?=e($user['role']==='kabag'?'Kepala Bagian':'Staf')?></span><h2><?=e($user['nama'])?></h2><p><?=e($user['email'])?> · Bergabung <?=e(format_date_id($user['created']))?></p></div></section>
<section class="stats-grid"><article class="stat-card"><span class="stat-icon blue"><i class="fa-solid fa-box-archive"></i></span><div class="stat-copy"><strong><?=$archiveCount?></strong><span>Arsip diunggah</span></div></article><article class="stat-card"><span class="stat-icon purple"><i class="fa-solid fa-at"></i></span><div class="stat-copy"><strong><?=e($user['username'])?></strong><span>Username</span></div></article><article class="stat-card"><span class="stat-icon green"><i class="fa-solid fa-shield-halved"></i></span><div class="stat-copy"><strong><?=e($user['role']==='kabag'?'Kabag':'Staf')?></strong><span>Hak akses</span></div></article><article class="stat-card"><span class="stat-icon orange"><i class="fa-regular fa-calendar"></i></span><div class="stat-copy"><strong><?=date('Y',strtotime($user['created']))?></strong><span>Tahun bergabung</span></div></article></section>
<section class="content-grid">
<article class="panel"><header class="panel-header"><div class="panel-title"><h2>Informasi profil</h2><p>Gunakan email aktif dan username yang mudah diingat.</p></div></header><form method="post" enctype="multipart/form-data" data-submit-lock><?=csrf_field()?><input type="hidden" name="action" value="update_profile"><div class="panel-body"><div class="form-grid"><div class="field field-full"><label>Foto profil</label><div class="upload-zone"><input type="file" name="foto_profile" accept=".jpg,.jpeg,.png,.webp" data-file-input="profilePreview"><i class="fa-regular fa-image"></i><strong>Pilih foto baru</strong><span>JPG, PNG, atau WEBP maksimal 3 MB</span></div><div class="upload-preview" id="profilePreview"></div></div><div class="field field-full"><label>Nama lengkap</label><input name="nama" value="<?=e($user['nama'])?>" maxlength="50" required></div><div class="field"><label>Email</label><input name="email" type="email" value="<?=e($user['email'])?>" required></div><div class="field"><label>Username</label><input name="username" value="<?=e($user['username'])?>" pattern="[A-Za-z0-9._-]{3,50}" required></div></div></div><footer class="modal-footer"><button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Profil</button></footer></form></article>
<aside class="panel"><header class="panel-header"><div class="panel-title"><h2>Ganti password</h2><p>Gunakan minimal 8 karakter.</p></div></header><form method="post" data-submit-lock><?=csrf_field()?><input type="hidden" name="action" value="change_password"><div class="panel-body"><div class="field"><label>Password saat ini</label><div class="input-wrap"><input class="input" id="currentPass" name="current_password" type="password" required><button class="icon-btn input-action" type="button" data-password-toggle="currentPass"><i class="fa-regular fa-eye"></i></button></div></div><div class="field" style="margin-top:14px"><label>Password baru</label><div class="input-wrap"><input class="input" id="newPass" name="new_password" type="password" minlength="8" required><button class="icon-btn input-action" type="button" data-password-toggle="newPass"><i class="fa-regular fa-eye"></i></button></div></div><div class="field" style="margin-top:14px"><label>Konfirmasi password baru</label><input name="confirm_password" type="password" minlength="8" required></div><div class="note-box" style="margin-top:15px"><i class="fa-solid fa-shield-halved"></i> Hindari menggunakan password yang sama dengan akun lain.</div></div><footer class="modal-footer"><button class="btn btn-outline" type="submit"><i class="fa-solid fa-key"></i> Ganti Password</button></footer></form></aside>
</section>
<?php render_app_end(); ?>