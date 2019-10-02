<?php

require('function.php');

debug('=========================================');
debug('=パスワード再発行（認証キー入力画面）');
debug('=========================================');
debugLogStart();

//====================================================================
//PW再発行画面（認証キー入力画面）処理開始
//====================================================================


//認証キーが発行されているかチェック
if(!empty($_SESSION['auth_email'])){
  
  //post送信された場合
  if(!empty($_POST)){
    debug('POST送信されました。');
    debug('POST情報。'.print_r($_POST,true));
  
    $token=$_POST['token'];
  
    //バリデーション
    validRequired($token,'token');
    validHalf($token,'token');

    if(empty($err_msg)){
      validLength($token,'token');
    
      if(empty($err_msg)){
        debug('バリデーションOK。');

        if($token !==$_SESSION['auth_token']){
          debug('認証キーが異なります。');
          $err_msg['token']=MSG16;
        }  

        if($_SESSION['auth_token_limit']<time()){
          debug('認証キーの期限が切れています。');
          $err_msg=MSG17;
        }  

        //新パスワード生成
        $pass=makeRandKey();

        debug('新パスワード：'.$pass);

        //DBへ新パスワードを更新
        try{
          $dbh=dbConnect();
          $sql="UPDATE users SET password=:pass WHERE email=:email AND delete_flg=0";
          $data=array(':pass'=>password_hash($pass,PASSWORD_DEFAULT),':email'=>$_SESSION['auth_email']);
          $stmt=queryPost($dbh,$sql,$data);

          if($stmt){
            debug('パスワードのDB更新成功');
            debug('ログイン画面へ遷移します。');

            //メール送信
            $from='tonenao@gmail.com';
            $to=$_SESSION['auth_email'];
            $subject='パスワード再発行通知 | PLAMONE';
            $comment=<<<EOT
            本メールアドレス宛にパスワードの再発行をいたしました。
            下記のURLにて再発行パスワードをご入力いただき、ログインください。

            ログインページ；http://localhost:8888/portfolio/gun/login.php
            再発行パスワード：{$pass}
            ※ログイン後、パスワードの変更をお願いいたします。
            
            ////////////////////////////////////////
            PLAMONE事務局
            URL http://plamone.com
            Email tonena@gmail.com
            ////////////////////////////////////////

            EOT;

            sendMail($from,$to,$subject,$comment);

        
              debug('セッションをクリアして、ログインページへ遷移します。');

              session_unset();
              $_SESSION['msg_success']=SUC03;

              debug('SESSION変数の中身');
              header('Location:login.php');
              exit();

            

          }else{
            debug('パスワードのDB更新失敗');
            $err_msg['common']=MSG07;
          }

        }catch(Exception $e){
          error_log('エラー発生'.$e->getMessage());
          $err_msg['common']=MSG07;
        }
             
      }
    }
  }
}



$siteTitle="パスワード再発行（認証キー送信） ";
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
          <p>ご指定のメールアドレスにお送りした【パスワード再発行認証メール】内にある「認証キー」をご入力下さい。</p>
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>

          <label class="<?php echo checkErr('token'); ?>">
            認証キー
            <input type="text" name="token">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('token'); ?>
          </div>

          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="変更画面へ">
          </div>
        </form>
      </div>
      <a href="passRemindSend.php">&lt; パスワード再発行メールを再度送信する</a>

    </section>

  </div>

  <?php 
require('footer.php');
?>
