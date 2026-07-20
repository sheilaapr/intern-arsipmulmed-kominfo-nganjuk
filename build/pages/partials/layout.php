<?php
require_once __DIR__ . '/../auth.php';
function nav_item(string $href,array|string $match,string $icon,string $label,string $active): string{
    $matches=(array)$match; $on=in_array($active,$matches,true);
    return '<a class="nav-item'.($on?' active':'').'" href="'.e($href).'"><i class="fa-solid '.e($icon).'"></i><span>'.e($label).'</span></a>';
}
function render_app_start(string $title,string $subtitle='',string $active=''): void{
    $flash=get_flash(); $active=$active?:basename($_SERVER['PHP_SELF']??'');
    $name=current_user_name(); $avatar=avatar_src($_SESSION['foto_profile']??null);
    $role=($_SESSION['role']??'')==='kabag'?'Kepala Bagian':'Staf'; ?>
<!DOCTYPE html><html lang="id"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> · Omah Tandang</title>
<link rel="icon" href="../assets/img/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/app.css?v=2.0.0">
</head><body>
<div class="app-shell"><div class="sidebar-backdrop" data-sidebar-close></div>
<aside class="sidebar" id="appSidebar">
  <div class="brand"><span class="brand-mark"><i class="fa-solid fa-house-chimney-window"></i></span><span class="brand-copy"><strong>Omah Tandang</strong><small>Arsip Multimedia</small></span><button class="icon-btn sidebar-close" data-sidebar-close><i class="fa-solid fa-xmark"></i></button></div>
  <nav class="sidebar-nav">
    <?=nav_item('dashboard.php','dashboard.php','fa-chart-pie','Dashboard',$active)?>
    <?=nav_item('kategori.php','kategori.php','fa-layer-group','Kategori',$active)?>
    <?=nav_item('arsip.php',['arsip.php','arsip_tambah.php'],'fa-photo-film','Arsip',$active)?>
    <?php if(($_SESSION['role']??'')==='kabag'): ?><?=nav_item('pengguna.php','pengguna.php','fa-users-gear','Anggota',$active)?><?php endif; ?>
    <div class="nav-label">Akun</div>
    <?=nav_item('profile.php','profile.php','fa-user-pen','Profil Saya',$active)?>
  </nav>
  <div class="sidebar-user">
    <span class="avatar avatar-sm"><?php if($avatar):?><img src="<?=e($avatar)?>" alt=""><?php else:?><?=e(initials($name))?><?php endif;?></span>
    <span class="sidebar-user-copy"><strong><?=e($name)?></strong><small><?=e($role)?></small></span>
    <form action="logout.php" method="post"><?=csrf_field()?><button class="icon-btn logout-btn" type="submit" data-confirm="Keluar dari aplikasi?"><i class="fa-solid fa-arrow-right-from-bracket"></i></button></form>
  </div>
</aside>
<main class="main-area">
<header class="topbar">
  <button class="icon-btn menu-trigger" data-sidebar-open><i class="fa-solid fa-bars-staggered"></i></button>
  <div class="page-heading"><span>Omah Tandang</span><h1><?=e($title)?></h1><?php if($subtitle):?><p><?=e($subtitle)?></p><?php endif;?></div>
  <div class="topbar-actions"><span class="date-chip"><i class="fa-regular fa-calendar"></i><?=e(format_date_id(date('Y-m-d')))?></span><a class="avatar avatar-sm" href="profile.php"><?php if($avatar):?><img src="<?=e($avatar)?>" alt=""><?php else:?><?=e(initials($name))?><?php endif;?></a></div>
</header><div class="page-content">
<?php if($flash):?><div class="toast toast-<?=e($flash['type'])?>" data-toast><i class="fa-solid <?=($flash['type']==='success'?'fa-circle-check':($flash['type']==='danger'?'fa-circle-xmark':'fa-circle-info'))?>"></i><span><?=e($flash['message'])?></span><button class="icon-btn" data-toast-close><i class="fa-solid fa-xmark"></i></button></div><?php endif;?>
<?php }
function render_app_end(string $extra=''): void{ ?>
</div><footer class="app-footer"><span>© <?=date('Y')?> Omah Tandang</span><span>Sistem Arsip Multimedia</span></footer></main></div>
<script src="../assets/js/app.js?v=2.0.0"></script><?=$extra?></body></html>
<?php } ?>