<?php

//====================================================================
//ログ関連
//====================================================================

//ログを取るか
ini_set('log_errors','on');
//ログの出力ファイルを指定
ini_set('error_log','php.log');



//====================================================================
//デバッグ関連
//====================================================================
$debug_flg=true;

function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//var_dumpの中身をlogに出力用関数
function debugD($str2,$str1=""){
  global $debug_flg;
  ob_start();
  var_dump($str2);
  $result=ob_get_contents();
  ob_end_clean();
  if(!empty($debug_flg)){
    error_log('デバッグD：'.$str1.print_r($result,true));
  }
}


//====================================================================
//セッション関連
//====================================================================

//セッションファイルの置き場を変更する（指定場所以外に置くと30日は削除されない）
session_save_path("../../../var/tmp/");
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているもに対してだけ100分の１の確率で削除）
ini_set('session.gc_maxlifetime',60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime',60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id(true);

//====================================================================
//画面表示関連
//====================================================================

function debugLogStart(){
debug('▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎');
debug('▶︎画面処理開始');
debug('▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎');
debug('セッションID：'.session_id());
debug('現在タイムスタンプ：'.time());
debug('セッション変数の中身：'.print_r($_SESSION,true));
if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
  debug('ログイン期限日時タイムスタンプ；'.($_SESSION['login_date']+$_SESSION['login_limit']));
}
  
  
}




//====================================================================
//エラー定数
//====================================================================

define('MSG01','入力必須です。');
define('MSG02','Emailの形式で入力してください。');
define('MSG03','パスワード（再入力）があっていません。');
define('MSG04','半角英数字のみご利用いただけます。');
define('MSG05','文字以上で入力してください。');
define('MSG06','文字以内で入力してください。');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08','そのEmailはすでに登録されています。');
define('MSG09','メールアドレスもしくはパスワードが違います。');
define('MSG10','電話番号の形式が違います。');
define('MSG11','郵便番号の形式が違います。');
define('MSG12','半角数値のみご利用できます。');
define('MSG13','古いパスワードが違います。');
define('MSG14','古いパスワードと同じです。');
define('MSG15','文字で入力してください。');
define('MSG16','正しくありません。');
define('MSG17','有効期限がきれています。');
define('MSG18','投稿するにはログインが必要です。');
define('MSG19','ログイン期限が切れています。再度ログインをして下さい。');
define('SUC01','パスワードを変更しました。');
define('SUC02','プロフィールを変更しました。');
define('SUC03','変更メールを送信しました。');
define('SUC04','登録しました。');






//====================================================================
//バリデーション関数関連
//====================================================================
//エラーメッセージ格納用の配列
$err_msg=array();

//バリデーション関数（未入力チェック）
function validRequired($str,$key){
  if($str ===""){
    global $err_msg;
    $err_msg[$key]=MSG01;
  }
}


//バリデーション関数（Email形式チェック）
function validEmail($str,$key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
    global $err_msg;
    $err_msg[$key]=MSG02;
  }
}


//バリデーション関数（Email重複チェック）
function validEmailDup($email){
  global $err_msg;
  //例外処理
  try{
    //DBへ接続
    $dbh=dbConnect();
    //SQL文作成
    $sql='SELECT count(*) FROM  users WHERE email=:email AND delete_flg=0';
    $data=array(':email'=>$email);
    //クエリ実行
    $stmt=queryPost($dbh,$sql,$data);
    //クエリ結果の値を配列形式で取得
    $result=$stmt->fetch(PDO::FETCH_ASSOC);
    //$resultの中身が入っているか判定
    if(!empty(array_shift($result))){
      $err_msg['email']=MSG08;
    }
    
  }catch(Exception $e){
    error_log('エラー発生；'.$e->getMessage());
    $err_msg['common']=MSG07;
  }
}



//バリデーション関数（同値チェック）
function validMatch($str1,$str2,$key){
  if($str1!==$str2){
    global $err_msg;
    $err_msg[$key]=MSG03;
  }
}


//バリデーション関数（最小文字数チェック）
function validMinLen($str,$key,$min=6){
  if(mb_strlen($str)<$min){
    global $err_msg;
    $err_msg[$key]=$min.MSG05;
  }
}


//バリデーション関数（最大文字数チェック）
function validMaxLen($str,$key,$max=255){
  if(mb_strlen($str)>$max){
    global $err_msg;
    $err_msg[$key]=$max.MSG06;
  }
}

//バリデーション関数（半角チェック）
function validHalf($str,$key){
  if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key]=MSG04;
  }
}

//バリデーション関数（電話番号形式チェック）
function validTel($str,$key){
  if(!preg_match("/0\d{1,4}\d{1,4}\d{4}/",$str)){
    if(!empty($str)){
      global $err_msg;
      $err_msg[$key]=MSG10;
    }
  }
}

//バリデーション関数（郵便番号形式チェック）
function validZip($str,$key){
  if(!preg_match("/^\d{7}$/",$str) ){
    if(!empty($str)){
      global $err_msg;
      $err_msg[$key]=MSG11;
    }
  }
}

//バリデーション関数（半角数字チェック）
function validNumber($str,$key){
  if(!preg_match("/^[0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key]=MSG12;
  }
}

//バリデーション関数（パスワードチェック）
function validPass($str,$key){
  global $err_msg;
  validMinLen($str,$key);
  validMaxLen($str,$key);
  validHalf($str,$key);
}

//固定長チェック
function validLength($str,$key,$length=8){
  if(mb_strlen($str) !== $length){
    global $err_msg;
    $err_msg[$key]=$length.MSG15;
  }
}

//====================================================================
//データベース
//====================================================================

//DB接続関数
function dbConnect(){
  $dsn='mysql:dbname=plamone;host=localhost;charset=utf8';
  $user='root';
  $password='root';
  $options=array(
    //SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    //デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    //バッファードクエリを使う（一度に接続セットを全て取得し、サーバー負荷を軽減）
    //SELECTで得た結果に対s知恵もrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true,
  );
  //PDOオブジェクト生成（DBへ接続）
  $dbh=new PDO($dsn,$user,$password,$options);
  return $dbh;
}



//SQL実行関数
function queryPost($dbh,$sql,$data){
  //クエリー作成
  $stmt=$dbh->prepare($sql);
  //プレースホルダに値をセットし、SQL文を実行
  if(!$stmt->execute($data)){
    debug('クエリ失敗しました。');
    debug('失敗したSQL：'.print_r($stmt,true));
    debug('SQLエラー（nikomuさん流）：'.print_r($stmt->errorInfo(),true));
    $err_msg['common']=MSG07;
    return false;
  }
  debug('クエリ成功。');
  return $stmt;
}


//ユーザー情報取得関数
function getUser($user_id){
  debug('ユーザー情報を取得します。');
  try{
    $dbh=dbConnect();
    $sql="SELECT * FROM users WHERE id=:user_id AND delete_flg=0";
    $data=array(':user_id'=>$user_id);
    $stmt=queryPost($dbh,$sql,$data);
    debug('$stmtの中身'.print_r($stmt,true));
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
    
  }catch(Exeption $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function getProduct($p_id){
  debug('商品情報を取得します。');
  
  try{
    $dbh=dbConnect();
    $sql="SELECT p.id,p.name,p.user_id,p.category_id,p.comment,p.pic1,p.pic2,p.pic3,u.username,c.name AS category FROM product AS p INNER JOIN users AS u ON u.id =p.user_id INNER JOIN category AS c ON p.category_id = c.id  WHERE p.id=:p_id AND p.delete_flg=0 AND c.delete_flg=0";
    $data=array(':p_id'=>$p_id);
    $stmt=queryPost($dbh,$sql,$data);
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function getCategory(){
  debug('カテゴリ情報を取得します。');
  try{
    $dbh=dbConnect();
    $sql='SELECT * FROM category';
    $data=array();
    $stmt=queryPost($dbh,$sql,$data);
    if($stmt){
      return $stmt->fetchall();
    }else{
      return 0;
    }

  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}


//index商品リスト関数
function getProductList($currentMinNum=0,$span=10,$c_id,$sort){
  debug('作品情報を取得します。');

  try{
    $dbh=dbConnect();
    $sql='SELECT id FROM product';
    if($c_id){
      $sql.=' WHERE category_id=:c_id';
      $data=array(':c_id'=>$c_id);
    }else{
      $data=array();
    }
    $stmt=queryPost($dbh,$sql,$data);
    $rst['total']=$stmt->rowCount();
    $rst['total_page']=ceil($rst['total']/$span);
    if(!$stmt){
      return false;      
    }


    $sql='SELECT p.id ,p.name ,p.pic1 ,u.username,count(l.product_id) AS count_like FROM product AS p INNER JOIN users AS u ON p.user_id=u.id LEFT JOIN p_like AS l ON l.product_id=p.id';
    if($c_id){
    $sql.=' WHERE p.category_id=:c_id';
    }
    $sql.=' GROUP BY p.id,p.name,p.pic1,u.username';
    
    switch($sort){
      case 0:
        $sql.=' ORDER BY p.id DESC';
        break;
      case 1:
        $sql.=' ORDER BY count_like DESC';
        break;
      case 2:
        $sql.=' ORDER BY count_like ASC';
        break;
    }

    $sql.=' LIMIT :span OFFSET :currentMinNum';
    debug('SQLの中身：'.$sql);
    $stmt=$dbh->prepare($sql);
    if($c_id){
    $stmt->bindValue(':c_id',$c_id,PDO::PARAM_INT);
    }
    $stmt->bindValue(':span',$span,PDO::PARAM_INT);
    $stmt->bindValue(':currentMinNum' , $currentMinNum,PDO::PARAM_INT);
    $stmt->execute();
    if($stmt){
      $rst['data']=$stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }
    
    if($stmt){
      $rst['data']=$stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }
  }catch(Exxeption $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}



function getMyPageList($u_id){
  debug('マイページ作品リスト取得開始。');
  try{
    $dbh=dbConnect();
    $sql="SELECT user_id FROM product WHERE user_id=:u_id";
    $data=array(':u_id'=>$u_id);
    $stmt=queryPost($dbh,$sql,$data);
    $rst['total']=$stmt->rowCount();
    debug('作品数：'.$rst['total']);


    $sql="SELECT p.id,p.name,p.pic1,count(l.product_id) AS count_like FROM product AS p LEFT JOIN p_like AS l ON p.id=l.product_id WHERE p.user_id=:user_id GROUP BY p.id,p.name,p.pic1 ORDER BY p.id ASC";
    $data=array(':user_id'=>$u_id);
    $stmt=queryPost($dbh,$sql,$data);
    debug('getMyPageListのSQLの中身：'.$sql);
    if($stmt){
      $rst['data']=$stmt->fetchAll();
      return $rst;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}



function getMyLikeList($u_id){
  debug('マイページお気に入りリスト取得開始。');
    try{
      $dbh=dbConnect();
      $sql="SELECT p.id,p.name,p.pic1,u.username FROM p_like AS l INNER JOIN product AS p ON p.id = l.product_id INNER JOIN users AS u ON p.user_id = u.id WHERE l.user_id=:u_id";
      $data=array(':u_id'=>$u_id);
      $stmt=queryPost($dbh,$sql,$data);
      if($stmt){
        return $stmt->fetchAll();
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
    }

}



//掲示板メッセージ取得関数
function getMessage($p_id){
  debug('投稿内容を取得します。');

  try{
    $dbh=dbConnect();
    $sql='SELECT m.builder_user,m.write_user,m.msg ,m.create_date ,u.username ,u.pic FROM message AS m INNER JOIN users AS u ON m.write_user = u.id WHERE product_id=:p_id';
    $data=array(':p_id'=>$p_id);
    $stmt=queryPost($dbh,$sql,$data);
    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}





//====================================================================
//メール送信
//====================================================================
function sendMail($from,$to,$subject,$comment){
  if(!empty($to) && !empty($subject) && !empty($comment)){
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");
    $result=mb_send_mail($to,$subject,$comment,"From:".$from);
    return $result;

    if($result){
      debug('メール送信しました。');
    }else{
      debug('エラー発生：メールの送信に失敗しました。');
    }
  }
}

//====================================================================
//画像ファイルアップロード
//====================================================================
function uploadImg($file,$key){
  debug('画像アップロード処理開始');
  debug('FILE情報：'.print_r($file,true));

  //アップロードファイルエラーに数値が格納されている場合
  if(!empty($file['name']) && is_int($file['error'])){

    try{
      //バリデーション
      //$file['error']の値を確認。配列内には「UPLOAD_ERR_OK」などの定数が入っている。
      //「UPLOAD_ERR_OK」などの定数はphpでファイルアップロード時に自動的に定義される。定数には値として0や1などの数値が入っている。

      switch($file['error']){
        case UPLOAD_ERR_OK: //OK
          break;
        case UPLOAD_ERR_NO_FILE: //ファイル未選択
          throw new RuntimeException('ファイルが選択されていません。');  
        case UPLOAD_ERR_INI_SIZE: //php.iniの定義の最大サイズが超過した場合
          throw new RuntimeException('ファイルサイズが大きすぎます。');
        case UPLOAD_ERR_FORM_SIZE: //フォーム定義の最大サイズが超過した場合
          throw new RuntimeException('ファイルサイズが大きすぎます。');
        default: //その他
          throw new RuntimeException('その他のエラーが発生しました。');
      }

      //$file['type']の値はブラウザ側で偽装が可能なので、MIMEタイプを事前でチェックする
      //exif_imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_JPEG」などの定数を返す。(この関数の前には、必ず＠をつける必要がある。エラーが出た場合に処理が止まってしまうが、、この＠があればエラーを無視することができる。)
      $type=@exif_imagetype($file['tmp_name']);
      debug('$typeの中身：'.$type);
      if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)){
        throw new RuntimeException('画像形式が違います。');
      }

      //ファイルデータからSHA-1ハッシュを取ってファイル名を決定し、ファイルを保存する。
      //ハッシュ化しておかないとアップロードされたファイル名をそのままで保存してしまうと同じファイル名がアップロードされる可能性があり、DBにパスを保存した場合、そちらの画像のパスなのか判断つかなくなってしまう。
      //image_type_toextension関数はファイルの拡張子を取得するもの
      $path='uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
      debug('$pathの中身：'.$path);

      //ファイルを移動する
      if(!move_uploaded_file($file['tmp_name'],$path)){
        throw new RuntimeException('ファイル保存時にエラーが発生しました。');
      }

      //保存したファイルパスのパーミッション（権限）を変更する。
      chmod($path,0644);

      debug('ファイルは正常にアップロードされました。');
      debug('ファイルパス：'.$path);
      return $path;

    }catch (RuntimeException $e){
      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key]=$e->getMessage();
    }
  }
}




//====================================================================
//その他関数
//====================================================================
function getErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return $err_msg[$key];
  }
}

function checkErr($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return 'err';
  }
}

//sessionを１回だけ取得できる関数
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data=$_SESSION[$key];
    $_SESSION[$key]="";
    return $data;
  }
}

//認証キー生成
function makeRandKey($length =8){
  $chars ='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  $str='';
  for($i=0;$i<$length;$i++){
    $str.=$chars[mt_rand(0,61)];
  }
  return $str;
}


//サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}


//フォーム入力保持
function getFormData($str){
  global $dbFormData;
  global $err_msg;
  //ユーザーデータがある場合。
  if(!empty($dbFormData[$str])){
    //フォームのエラーがある場合
    if(!empty($err_msg[$str])){
      debug('$err_msgの中身：'.print_r($err_msg,true));
      return sanitize($_POST[$str]);
    //POST送信があり、ユーザーデータと異なり、$str項目でエラーは無いが、他のフォームでエラーがある場合
    }else{
      if(isset($_POST[$str]) && $_POST[$str]!==$dbFormData[$str]){
        return sanitize($_POST[$str]);
    //ユーザーデータがあるが、そもそも変更していない場合
      }else{
        return sanitize($dbFormData[$str]);
      }
    }
  //ユーザーデータがなく、POST送信がある場合。
  }else{
    if(isset($_POST[$str])){
      return sanitize($_POST[$str]);
    }
  }
}

//GETパラメータから指定したものを削除する
function appendGetParam($arr_del_key){
  if(!empty($_GET)){
    $str="?"; 
      foreach($_GET as $key =>$val){
        if(!in_array($key,$arr_del_key,true)){
        $str.=$key ."=".$val."&";
      }
    }
    $str=mb_substr($str,0,-1,"utf-8");
    return $str;
  }
}



//ログインしているかどうかの判定
function isLogin(){

  global $err_msg;
  if(!empty($_SESSION['user_id'])){
    debug('ログイン済みユーザーです。');

    if($_SESSION['login_date']+$_SESSION['login_limit']>time()){
      debug('ログイン有効期限内です。');
      return true;
    }else{
      debug('ログイン期限オーバーです。');
      return false;
      session_destroy();
    }

  }else{
    debug('未ログインです。');
    return false;
  }
}


//お気に入り登録したかどうかの判定
function isLike($p_id,$user_id){
  debug('お気に入り情報があるか確認します。');
  debug('作品ID：'.$p_id);
  debug('ユーザーID：'.$user_id);

  try{
    $dbh=dbConnect();
    $sql="SELECT * FROM p_like WHERE product_id=:p_id AND user_id=:user_id";
    $data=array('p_id'=>$p_id,':user_id'=>$user_id);
    $stmt=queryPost($dbh,$sql,$data);
    $result=$stmt->fetch(PDO::FETCH_ASSOC);

    if($stmt->rowCount()){
      debug('お気に入りです。');
      return true;
    }else{
      debug('お気に入りではありません。');
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生'.$e->getMessage());
    return false;
  }
}

//お気に入り数カウント関数
function countLike($p_id){
  try{
    $dbh=dbConnect();
    $sql="SELECT * FROM p_like WHERE product_id =:p_id";
    $data=array(':p_id'=>$p_id);
    $stmt=queryPost($dbh,$sql,$data);
    if($stmt){
    return $stmt->rowCount();
    }else{
      return false;
    }

  }catch(Exceptin $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

?>
