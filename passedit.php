<?php

require('function.php');

debug('=========================================');
debug('=パスワード変更');
debug('=========================================');
debugLogStart();

require('auth.php');

//================================
// パスワード編集画面処理
//================================

$db_data=getUser($_SESSION['user_id']);

//POSTされた場合
if(!empty($_POST)){
debug('POST送信があります。');

//POSTされた内容を変数に格納
$pass_old=$_POST['pass_old'];
$pass_new=$_POST['pass_new'];
$pass_new_re=$_POST['pass_new_re'];

//未入力チェック
validRequired($pass_old,'pass_old');
validRequired($pass_new,'pass_new');
validRequired($pass_new_re,'pass_new_re');



if(empty($err_msg)){
  //DBのパスワードと一致しているかチェック
  if(!password_verify($pass_old,$db_data['password'])){
    //パスワードが違います。
    $err_msg['pass_old']=MSG13;
  }

  //バリデーション 
  validMatch($pass_new,$pass_new_re,'pass_new_re');
  validPass($pass_new,'pass_new');

  //古いパスと新しパスが同値かチェック
  if($pass_old === $pass_new){
    $err_msg['pass_new']=MSG14;
  }


  if(empty($err_msg)){
    debug('バリデーションOK');

    try{
      $dbh=dbConnect();
      $sql='UPDATE users SET password=:password WHERE id=:user_id;';
      $data=array(':password'=>password_hash($pass_new,PASSWORD_DEFAULT),':user_id'=>$db_data['id']);
      $stmt=queryPost($dbh,$sql,$data);

      if($stmt){
        $_SESSION['msg_success']=SUC01;
        
        //メールを送信
        $mailToName=($db_data['username'])?$db_data['username']."さま":"";
        $to=$db_data['email'];
        $subject="パスワード変更通知 | PLAMONE";
        $from="tonenao@gmail.com";
        $comment= <<<EOT
        {$mailToName}

        いつもご利用ありがとうございます。
        PLAMONE事務局です。

        パスワードが変更されました。
        引き続きよろしくお願いいたします。

        ////////////////////////////////////////
        PLAMONE事務局
        URL http://plamone.com
        Email tonena@gmail.com
        ////////////////////////////////////////

        EOT;

        sendMail($from,$to,$subject,$comment);

        header("Location:mypage.php");
        exit();

      }

    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common']=MSG07;
    }
    
    

  }

}


}






$siteTitle="パスワード変更";
require('head.php');

?>


<body>


  <!--メニュー-->
  <?php
  require('header.php');
  ?>




  <!--メインコンテンツ-->
  <div id="contents" class="site-width">

    <!--  メイン-->
    <section id="main-login">
      <div class="form-container">

        <form action="" class="form" method="post">
          <h2 class="title">パスワード変更</h2>

          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>

          <label class="<?php echo checkErr('pass_old'); ?>">
            古いパスワード
            <input type="password" name="pass_old" value="<?php echo getFormData('pass_old'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('pass_old'); ?>
          </div>

          <label class="<?php echo checkErr('pass_new'); ?>">
            新しいパスワード
            <input type="password" name="pass_new" value="<?php echo getFormData('pass_new'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('pass_new'); ?>
          </div>

          <label class="<?php echo checkErr('pass_new_re'); ?>">
            新しいパスワード(再入力)
            <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('pass_new_re'); ?>
          </div>

          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="変更する">
          </div>

        </form>

      </div>
      <a href="mypage.php">&lt; マイページに戻る</a>
    </section>

  </div>

  <?php 
require('footer.php');
?>
