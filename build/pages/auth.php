<?php
require_once __DIR__ . '/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    ]);
    session_start();
}
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function redirect(string $path): void { header('Location: '.$path); exit; }
function set_flash(string $type, string $message): void { $_SESSION['flash']=['type'=>$type,'message'=>$message]; }
function get_flash(): ?array { $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return is_array($f)?$f:null; }
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf_token" value="'.e(csrf_token()).'">'; }
function verify_csrf(): void {
    $token=(string)($_POST['csrf_token']??'');
    if ($token==='' || !hash_equals(csrf_token(),$token)) {
        http_response_code(419); exit('Sesi formulir tidak valid. Muat ulang halaman.');
    }
}
function is_logged_in(): bool { return isset($_SESSION['id'],$_SESSION['username'],$_SESSION['role']); }
function require_login(): void { if(!is_logged_in()){ set_flash('warning','Silakan masuk terlebih dahulu.'); redirect('index.php'); } }
function require_role(string $role): void {
    require_login();
    if(($_SESSION['role']??'')!==$role){ set_flash('danger','Anda tidak memiliki izin membuka halaman tersebut.'); redirect('dashboard.php'); }
}
function current_user_name(): string { return (string)($_SESSION['nama']??$_SESSION['username']??'Pengguna'); }
function db_scalar(mysqli $db,string $sql,$default=0){
    $r=mysqli_query($db,$sql); if(!$r)return $default; $row=mysqli_fetch_row($r); return $row[0]??$default;
}
function format_date_id(?string $date,bool $time=false): string {
    if(!$date || strtotime($date)===false)return '-';
    $m=[1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
    $t=strtotime($date); $out=date('d',$t).' '.$m[(int)date('n',$t)].' '.date('Y',$t);
    return $time?$out.', '.date('H:i',$t):$out;
}
function text_length(string $value): int { return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value); }
function initials(string $name): string {
    $parts=preg_split('/\s+/',trim($name))?:[]; $out='';
    foreach(array_slice($parts,0,2) as $p){
        $char=function_exists('mb_substr')?mb_substr($p,0,1):substr($p,0,1);
        $out.=function_exists('mb_strtoupper')?mb_strtoupper($char):strtoupper($char);
    }
    return $out?:'U';
}
function is_image_extension(string $ext): bool { return in_array(strtolower($ext),['jpg','jpeg','png','gif','webp'],true); }
function avatar_src(?string $file): ?string {
    if(!$file)return null; $name=basename($file); $path=__DIR__.'/uploads/profile/'.$name;
    return is_file($path)?'uploads/profile/'.rawurlencode($name):null;
}
function safe_folder(string $title): string {
    $slug=preg_replace('/[^a-z0-9]+/i','_',strtolower(trim($title)))?:'arsip';
    return trim($slug,'_').'_'.date('Ymd_His').'_'.bin2hex(random_bytes(2));
}
function normalize_uploads(array $files): array {
    $out=[]; $names=$files['name']??[]; if(!is_array($names))$names=[$names];
    foreach($names as $i=>$name){ if($name==='')continue; $out[]=[
      'name'=>$name,
      'tmp_name'=>is_array($files['tmp_name']??null)?($files['tmp_name'][$i]??''):($files['tmp_name']??''),
      'error'=>is_array($files['error']??null)?($files['error'][$i]??UPLOAD_ERR_NO_FILE):($files['error']??UPLOAD_ERR_NO_FILE),
      'size'=>is_array($files['size']??null)?(int)($files['size'][$i]??0):(int)($files['size']??0),
      'type'=>is_array($files['type']??null)?($files['type'][$i]??''):($files['type']??'')
    ]; }
    return $out;
}
function store_archive_uploads(array $input,string $folder,int $max=25): array {
    $files=normalize_uploads($input);
    if(count($files)>$max)throw new RuntimeException("Maksimal $max file.");
    $allowed=[
      'jpg'=>['image/jpeg'],'jpeg'=>['image/jpeg'],'png'=>['image/png'],'gif'=>['image/gif'],'webp'=>['image/webp'],
      'pdf'=>['application/pdf'],'doc'=>['application/msword','application/octet-stream'],
      'docx'=>['application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/zip','application/octet-stream']
    ];
    $dir=__DIR__.'/uploads/'.$folder;
    if(!is_dir($dir) && !mkdir($dir,0775,true))throw new RuntimeException('Folder upload tidak dapat dibuat.');
    $finfo=class_exists('finfo')?new finfo(FILEINFO_MIME_TYPE):null; $saved=[];
    try{
      foreach($files as $file){
        if((int)$file['error']!==UPLOAD_ERR_OK)throw new RuntimeException('Salah satu file gagal diterima.');
        if((int)$file['size']<=0 || (int)$file['size']>10*1024*1024)throw new RuntimeException('Ukuran file maksimal 10 MB.');
        if(!is_uploaded_file($file['tmp_name']))throw new RuntimeException('Sumber upload tidak valid.');
        $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
        if(!isset($allowed[$ext]))throw new RuntimeException('Format file tidak didukung: '.$file['name']);
        $mime=$finfo?(string)$finfo->file($file['tmp_name']):(string)$file['type'];
        if($mime!=='' && !in_array($mime,$allowed[$ext],true))throw new RuntimeException('Isi file tidak sesuai format: '.$file['name']);
        $base=preg_replace('/[^a-zA-Z0-9_-]+/','_',pathinfo($file['name'],PATHINFO_FILENAME))?:'file';
        $final=trim($base,'_').'_'.bin2hex(random_bytes(5)).'.'.$ext;
        if(!move_uploaded_file($file['tmp_name'],$dir.'/'.$final))throw new RuntimeException('File gagal disimpan.');
        $saved[]=['filename'=>$folder.'/'.$final,'tipe_file'=>strtoupper($ext)];
      }
    }catch(Throwable $err){
      foreach($saved as $s){$p=__DIR__.'/uploads/'.$s['filename'];if(is_file($p))@unlink($p);}
      throw $err;
    }
    return $saved;
}
function delete_uploaded_file(?string $relative): void {
    if(!$relative)return; $root=realpath(__DIR__.'/uploads'); $path=realpath(__DIR__.'/uploads/'.ltrim($relative,'/\\'));
    if(!$root||!$path||!str_starts_with($path,$root.DIRECTORY_SEPARATOR))return;
    if(is_file($path))@unlink($path);
    $parent=dirname($path); if($parent!==$root && is_dir($parent)){
      $left=array_diff(scandir($parent)?:[],['.','..']); if(!$left)@rmdir($parent);
    }
}
?>