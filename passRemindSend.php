<?php

require('function.php');

debug('=========================================');
debug('=パスワード再発行（email送信）');
debug('=========================================');
debugLogStart();


//====================================================================
//PW再発行画面（email入力画面）処理開始
//====================================================================



//POST送信された場合
if(!empty($_POST)){
  debug('POST送信されました。');

  //POSTされた内容を変数に格納
  $email=$_POST['email'];

  //各種バリデーション
  validRequired($email,'email');
  validMaxLen($email,'email');

  if(empty($err_msg)){
    validEmail($email,'email');

    if(empty($err_msg)){
      //DBにemailがあるかチェック
      try{
        $dbh=dbConnect();
        $sql="SELECT count(*) FROM users WHERE email=:email AND delete_flg=0";
        $data=array(':email'=>$email);
        $stmt=queryPost($dbh,$sql,$data);
        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        if(array_shift($result)){
          
          debug('DB登録あり。');
          //認証キー生成
          $token=makeRandKey();
          
          
          //メール送信
          
          $from='tonenao@gmail.com';
          $to=$email;
          $subject='パスワード認証キー発行通知 | PLAMONE';
          $comment=<<<EOT
          本メールアドレス宛にパスワード再発行のご依頼がありました。
          下記のURLにて認証キーを誤入力いただくとパスワードが再発行されます。

          パスワード再発行認証キー入力ページ；http://localhost:8888/portfolio/gun/passRemindRecieve.php
          認証キー：{$token}
          ※認証キーの有効期限は30分でございます。
          
          認証キーを再発行されたい場合は下記ページより再度お手続きをお願いいたします。
          http://localhost:8888/portfolio/gun/passRemindSend.php

          ////////////////////////////////////////
          PLAMONE事務局
          URL http://plamone.com
          Email tonena@gmail.com
          ////////////////////////////////////////
          
          EOT;
          
          sendMail($from,$to,$subject,$comment);

            $_SESSION['auth_email']=$email;
            $_SESSION['auth_token']=$token;
            $_SESSION['auth_token_limit']=time()+(60*30);//現在時刻より30分後のUNIXタイムスタンプ
            
            debug('セッション変数の中身：'.print_r($_SESSION,true));
            
            header("Location:passRemindRecieve.php");
            exit();

            
          }else{
            debug('DB登録のないemailが入力されました。');
            $err_msg['email']=MSG16;
        }



      }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['commmon']=MSG07;
      }



  }
 }

}




$siteTitle="パスワード再発行（email送信） ";
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
          <p>ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送りいたします。</p>

          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>

          <label class="<?php echo checkErr('email'); ?>">
            Email
            <input type="text" name="email" value="<?php echo getFormData('email') ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('email'); ?>
          </div>

          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="送信する">
          </div>
        </form>
      </div>
      <a href="mypage.php">&lt; マイページに戻る</a>

    </section>

  </div>

  <?php 
require('footer.php');
?>
