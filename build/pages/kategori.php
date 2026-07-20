<?php
require_once __DIR__.'/auth.php';
require_once __DIR__.'/partials/layout.php';
require_login();

if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); $action=(string)($_POST['action']??'');
 if($action==='create'||$action==='update'){
   $id=(int)($_POST['id_kategori']??0); $nama=trim((string)($_POST['nama_kategori']??'')); $deskripsi=trim((string)($_POST['deskripsi']??''));
   if($nama===''||text_length($nama)>100||text_length($deskripsi)>500){set_flash('danger','Nama wajib diisi (maks. 100 karakter) dan deskripsi maksimal 500 karakter.');redirect('kategori.php');}
   $check=$koneksi->prepare('SELECT id_kategori FROM kategori WHERE LOWER(nama_kategori)=LOWER(?) AND id_kategori<>? LIMIT 1');$check->bind_param('si',$nama,$id);$check->execute();
   if($check->get_result()->num_rows){set_flash('warning','Nama kategori sudah digunakan.');redirect('kategori.php');}
   if($action==='create'){$q=$koneksi->prepare('INSERT INTO kategori(nama_kategori,deskripsi) VALUES(?,?)');$q->bind_param('ss',$nama,$deskripsi);$ok=$q->execute();}
   else{$q=$koneksi->prepare('UPDATE kategori SET nama_kategori=?,deskripsi=? WHERE id_kategori=?');$q->bind_param('ssi',$nama,$deskripsi,$id);$ok=$q->execute();}
   set_flash($ok?'success':'danger',$ok?($action==='create'?'Kategori berhasil ditambahkan.':'Kategori berhasil diperbarui.'):'Data kategori gagal disimpan.');redirect('kategori.php');
 }
 if($action==='delete'){
   $id=(int)($_POST['id_kategori']??0);$q=$koneksi->prepare('SELECT COUNT(*) total FROM arsip WHERE id_kategori=?');$q->bind_param('i',$id);$q->execute();$count=(int)$q->get_result()->fetch_assoc()['total'];
   if($count>0){set_flash('warning',"Kategori tidak dapat dihapus karena masih berisi $count arsip.");redirect('kategori.php');}
   $q=$koneksi->prepare('DELETE FROM kategori WHERE id_kategori=?');$q->bind_param('i',$id);$ok=$q->execute();
   set_flash($ok?'success':'danger',$ok?'Kategori berhasil dihapus.':'Kategori gagal dihapus.');redirect('kategori.php');
 }
}

$items=[];$r=mysqli_query($koneksi,"SELECT k.id_kategori,k.nama_kategori,k.deskripsi,COUNT(DISTINCT a.id_arsip) archive_count,COUNT(af.id_file) file_count,MAX(a.created_at) last_activity
FROM kategori k LEFT JOIN arsip a ON a.id_kategori=k.id_kategori LEFT JOIN arsip_file af ON af.id_arsip=a.id_arsip
GROUP BY k.id_kategori,k.nama_kategori,k.deskripsi ORDER BY k.nama_kategori");
if($r)while($x=mysqli_fetch_assoc($r))$items[]=$x;
$total=count($items);$filled=0;$files=0;foreach($items as $x){if((int)$x['archive_count']>0)$filled++;$files+=(int)$x['file_count'];}

render_app_start('Kategori','Kelompokkan dokumentasi agar arsip mudah ditemukan.','kategori.php'); ?>
<section class="hero"><div class="hero-copy"><span class="hero-kicker">Struktur Arsip</span><h2>Kategori yang rapi membuat koleksi terasa lebih bernilai.</h2><p>Buat kategori berdasarkan bidang, program, atau jenis kegiatan. Setiap kategori dapat menampung banyak arsip dan file pendukung.</p></div><div class="hero-actions"><button class="btn btn-light" data-modal-open="createCategory"><i class="fa-solid fa-plus"></i> Tambah Kategori</button></div></section>
<section class="stats-grid">
<article class="stat-card"><span class="stat-icon blue"><i class="fa-solid fa-layer-group"></i></span><div class="stat-copy"><strong><?=$total?></strong><span>Total kategori</span></div></article>
<article class="stat-card"><span class="stat-icon green"><i class="fa-solid fa-folder-open"></i></span><div class="stat-copy"><strong><?=$filled?></strong><span>Kategori terisi</span></div></article>
<article class="stat-card"><span class="stat-icon purple"><i class="fa-solid fa-paperclip"></i></span><div class="stat-copy"><strong><?=$files?></strong><span>Total file</span></div></article>
<article class="stat-card"><span class="stat-icon orange"><i class="fa-solid fa-folder-minus"></i></span><div class="stat-copy"><strong><?=$total-$filled?></strong><span>Kategori kosong</span></div></article>
</section>
<section class="panel section-gap"><header class="panel-header"><div class="panel-title"><h2>Daftar kategori</h2><p>Klik buka arsip untuk melihat seluruh dokumentasi.</p></div><div class="toolbar"><label class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input class="input" type="search" placeholder="Cari kategori..." data-live-search=".category-card" data-empty-target="#categorySearchEmpty"></label><button class="btn btn-primary" data-modal-open="createCategory"><i class="fa-solid fa-plus"></i> Tambah</button></div></header><div class="panel-body">
<?php if($items):?><div class="category-grid"><?php foreach($items as $i=>$item):?>
<article class="category-card" data-search="<?=e($item['nama_kategori'].' '.$item['deskripsi'])?>">
<div class="category-head"><span class="folder-icon"><i class="fa-solid fa-folder"></i></span><span class="badge <?=((int)$item['archive_count']?'badge-success':'')?>"><?=(int)$item['archive_count']?> arsip</span></div>
<h3><?=e($item['nama_kategori'])?></h3><p><?=e($item['deskripsi']?:'Belum ada deskripsi untuk kategori ini.')?></p>
<div class="card-meta"><span><i class="fa-solid fa-paperclip"></i> <?=(int)$item['file_count']?> file</span><span><i class="fa-regular fa-clock"></i> <?=e($item['last_activity']?format_date_id($item['last_activity']):'Belum aktif')?></span></div>
<div class="card-actions"><a class="btn btn-secondary btn-sm" href="arsip_tambah.php?id_kategori=<?=(int)$item['id_kategori']?>"><i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Arsip</a>
<button class="btn btn-outline btn-sm" type="button" onclick='editCategory(<?=json_encode(["id"=>(int)$item["id_kategori"],"nama"=>$item["nama_kategori"],"deskripsi"=>$item["deskripsi"]],JSON_HEX_APOS|JSON_HEX_QUOT)?>)'><i class="fa-solid fa-pen"></i></button>
<form method="post" style="margin:0"><?=csrf_field()?><input type="hidden" name="action" value="delete"><input type="hidden" name="id_kategori" value="<?=(int)$item['id_kategori']?>"><button class="btn btn-danger btn-sm" type="submit" data-confirm="Hapus kategori <?=e($item['nama_kategori'])?>? Kategori yang berisi arsip tidak dapat dihapus."><i class="fa-solid fa-trash"></i></button></form></div>
</article><?php endforeach;?></div><div id="categorySearchEmpty" class="empty-state" hidden><i class="fa-solid fa-magnifying-glass"></i><h3>Kategori tidak ditemukan</h3><p>Coba gunakan kata pencarian lain.</p></div>
<?php else:?><div class="empty-state"><i class="fa-solid fa-layer-group"></i><h3>Belum ada kategori</h3><p>Buat kategori pertama untuk mulai menyimpan dokumentasi.</p><button class="btn btn-primary" data-modal-open="createCategory">Tambah kategori</button></div><?php endif;?>
</div></section>

<div class="modal" id="createCategory"><div class="modal-panel"><header class="modal-header"><div class="modal-header-copy"><h2>Tambah kategori</h2><p>Buat wadah baru untuk mengelompokkan arsip.</p></div><button class="icon-btn" data-modal-close><i class="fa-solid fa-xmark"></i></button></header><form method="post" data-submit-lock><?=csrf_field()?><input type="hidden" name="action" value="create"><div class="modal-body"><div class="field"><label>Nama kategori</label><input name="nama_kategori" maxlength="100" required placeholder="Contoh: Pembinaan Kader"></div><div class="field" style="margin-top:14px"><label>Deskripsi</label><textarea name="deskripsi" maxlength="500" placeholder="Jelaskan isi kategori secara singkat"></textarea></div></div><footer class="modal-footer"><button class="btn btn-outline" type="button" data-modal-close>Batal</button><button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan</button></footer></form></div></div>
<div class="modal" id="editCategory"><div class="modal-panel"><header class="modal-header"><div class="modal-header-copy"><h2>Edit kategori</h2><p>Perbarui nama atau deskripsi kategori.</p></div><button class="icon-btn" data-modal-close><i class="fa-solid fa-xmark"></i></button></header><form method="post" data-submit-lock><?=csrf_field()?><input type="hidden" name="action" value="update"><input type="hidden" name="id_kategori" id="editCategoryId"><div class="modal-body"><div class="field"><label>Nama kategori</label><input name="nama_kategori" id="editCategoryName" maxlength="100" required></div><div class="field" style="margin-top:14px"><label>Deskripsi</label><textarea name="deskripsi" id="editCategoryDescription" maxlength="500"></textarea></div></div><footer class="modal-footer"><button class="btn btn-outline" type="button" data-modal-close>Batal</button><button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Perbarui</button></footer></form></div></div>
<?php
$script=<<<'JS'
<script>
function editCategory(data){document.getElementById('editCategoryId').value=data.id;document.getElementById('editCategoryName').value=data.nama||'';document.getElementById('editCategoryDescription').value=data.deskripsi||'';openModal('editCategory')}
</script>
JS;
render_app_end($script); ?>