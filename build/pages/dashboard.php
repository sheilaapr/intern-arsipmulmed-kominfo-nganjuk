<?php
require_once __DIR__.'/auth.php';
require_once __DIR__.'/partials/layout.php';
require_login();
$totalKategori=(int)db_scalar($koneksi,'SELECT COUNT(*) FROM kategori');
$totalArsip=(int)db_scalar($koneksi,'SELECT COUNT(*) FROM arsip');
$totalFile=(int)db_scalar($koneksi,'SELECT COUNT(*) FROM arsip_file');
$totalUser=(int)db_scalar($koneksi,'SELECT COUNT(*) FROM users');
$recent=[];$r=mysqli_query($koneksi,"SELECT a.id_arsip,a.judul,a.tanggal_acara,a.created_at,a.id_kategori,k.nama_kategori,u.nama uploader,
(SELECT af.filename FROM arsip_file af WHERE af.id_arsip=a.id_arsip ORDER BY af.id_file LIMIT 1) first_file,
(SELECT af.tipe_file FROM arsip_file af WHERE af.id_arsip=a.id_arsip ORDER BY af.id_file LIMIT 1) first_type,
(SELECT COUNT(*) FROM arsip_file af WHERE af.id_arsip=a.id_arsip) file_count
FROM arsip a JOIN kategori k ON k.id_kategori=a.id_kategori LEFT JOIN users u ON u.id=a.uploaded_by
ORDER BY a.created_at DESC,a.id_arsip DESC LIMIT 6");
if($r)while($x=mysqli_fetch_assoc($r))$recent[]=$x;
$categories=[];$r=mysqli_query($koneksi,"SELECT k.id_kategori,k.nama_kategori,k.deskripsi,COUNT(a.id_arsip) archive_count
FROM kategori k LEFT JOIN arsip a ON a.id_kategori=k.id_kategori GROUP BY k.id_kategori,k.nama_kategori,k.deskripsi
ORDER BY archive_count DESC,k.nama_kategori LIMIT 5");
if($r)while($x=mysqli_fetch_assoc($r))$categories[]=$x;
render_app_start('Dashboard','Ringkasan aktivitas dan kondisi arsip multimedia.','dashboard.php'); ?>
<section class="hero"><div class="hero-copy"><span class="hero-kicker">Selamat datang, <?=e(current_user_name())?></span><h2>Semua dokumentasi penting, tersusun dalam satu tempat.</h2><p>Gunakan kategori untuk mengelompokkan kegiatan, lalu unggah foto dan dokumen agar arsip organisasi tetap rapi dan mudah ditemukan.</p></div><div class="hero-actions"><a class="btn btn-light" href="arsip.php"><i class="fa-solid fa-photo-film"></i> Buka Arsip</a><a class="btn btn-secondary" href="kategori.php"><i class="fa-solid fa-layer-group"></i> Kelola Kategori</a></div></section>
<section class="stats-grid">
<article class="stat-card"><span class="stat-icon blue"><i class="fa-solid fa-layer-group"></i></span><div class="stat-copy"><strong><?=$totalKategori?></strong><span>Total kategori</span></div></article>
<article class="stat-card"><span class="stat-icon purple"><i class="fa-solid fa-box-archive"></i></span><div class="stat-copy"><strong><?=$totalArsip?></strong><span>Total arsip kegiatan</span></div></article>
<article class="stat-card"><span class="stat-icon green"><i class="fa-solid fa-paperclip"></i></span><div class="stat-copy"><strong><?=$totalFile?></strong><span>Foto dan dokumen</span></div></article>
<article class="stat-card"><span class="stat-icon orange"><i class="fa-solid fa-users"></i></span><div class="stat-copy"><strong><?=$totalUser?></strong><span>Anggota terdaftar</span></div></article>
</section>
<section class="content-grid">
<article class="panel"><header class="panel-header"><div class="panel-title"><h2>Arsip terbaru</h2><p>Dokumentasi yang terakhir ditambahkan.</p></div><a class="btn btn-outline btn-sm" href="arsip.php">Lihat semua <i class="fa-solid fa-arrow-right"></i></a></header><div class="panel-body">
<?php if($recent):?><div class="list"><?php foreach($recent as $item):?><a class="list-row" href="arsip_tambah.php?id_kategori=<?=(int)$item['id_kategori']?>"><span class="list-icon"><?php if($item['first_file']&&is_image_extension((string)$item['first_type'])):?><img src="uploads/<?=e($item['first_file'])?>" alt="" style="width:100%;height:100%;object-fit:cover"><?php else:?><i class="fa-solid fa-file-lines"></i><?php endif;?></span><span class="list-copy"><strong><?=e($item['judul'])?></strong><span><?=e($item['nama_kategori'])?> · <?=(int)$item['file_count']?> file · <?=e($item['uploader']?:'-')?></span></span><span class="badge badge-primary"><?=e(format_date_id($item['tanggal_acara']))?></span></a><?php endforeach;?></div>
<?php else:?><div class="empty-state"><i class="fa-regular fa-folder-open"></i><h3>Belum ada arsip</h3><p>Mulai dengan membuat kategori dan menambahkan dokumentasi.</p><a class="btn btn-primary" href="kategori.php">Buat kategori</a></div><?php endif;?></div></article>
<aside class="panel"><header class="panel-header"><div class="panel-title"><h2>Kategori teraktif</h2><p>Urutan berdasarkan jumlah arsip.</p></div></header><div class="panel-body">
<?php if($categories):?><div class="list"><?php foreach($categories as $cat):?><a class="list-row" href="arsip_tambah.php?id_kategori=<?=(int)$cat['id_kategori']?>"><span class="list-icon"><i class="fa-solid fa-folder"></i></span><span class="list-copy"><strong><?=e($cat['nama_kategori'])?></strong><span><?=e($cat['deskripsi']?:'Tanpa deskripsi')?></span></span><span class="badge"><?=(int)$cat['archive_count']?> arsip</span></a><?php endforeach;?></div><?php else:?><div class="empty-state"><i class="fa-solid fa-layer-group"></i><h3>Belum ada kategori</h3><p>Kategori akan tampil di sini.</p></div><?php endif;?></div></aside>
</section>
<?php render_app_end(); ?>