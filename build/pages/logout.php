<?php
require_once __DIR__.'/auth.php';
if($_SERVER['REQUEST_METHOD']!=='POST')redirect('dashboard.php');
verify_csrf(); $_SESSION=[];
if(ini_get('session.use_cookies')){$p=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);}
session_destroy(); session_start(); set_flash('success','Anda telah keluar dari aplikasi.'); redirect('index.php');
?>