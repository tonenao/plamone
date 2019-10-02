<?php

//共通変数・関数ファイルを読み込み
require('function.php');

debug('=========================================');
debug('=ログインページ');
debug('=========================================');
debugLogStart();

//ログイン認証
require('auth.php');

//====================================================================
//ログイン画面処理
//====================================================================
//POSTされていた場合
if(!empty($_POST)){
  debug('POST送信があります。');
  
  //変数にユーザー情報を代入
  $email=$_POST['email'];
  $pass=$_POST['pass'];
  $pass_save=(!empty($_POST['pass_save']));
  
  //バリデーション
  validRequired($email,'email');
  validRequired($pass,'pass');
  
  
  if(empty($err_msg)){
    debug('バリデーションOKです。');
    
    //例外処理
    try{
      $dbh=dbConnect();
      $sql='SELECT password,id FROM users WHERE email=:email';
      $data=array(':email'=>$email);
      $stmt=queryPost($dbh,$sql,$data);
      $result=$stmt->fetch(PDO::FETCH_ASSOC);
      
      debug('クエリ結果（$result）の中身'.print_r($result,true));
      
      if(!empty($result) && password_verify($pass,$result['password'])){
        debug('パスワードが一致しました。');
        
        //デフォルトログイン有効期限を1時間に設定
        $sesLimit=60*60;
        //最終ログイン日時を現在日時に設定
        $_SESSION['login_date']=time();
        //ログイン保持にチェックがある場合
        if($pass_save){
          debug('ログイン保持にチェックがあります。');
          //ログイン有効期限を30日に設定
          $_SESSION['login_limit']=$sesLimit*24*30;
        }else{
          debug('ログイン保持チェックはありません。');
          //ログイン有効期限を1時間後に設定
          $_SESSION['login_limit']=$sesLimit;
        }
        
        //ユーザーIDを格納
        $_SESSION['user_id']=$result['id'];
        
        debug('セッション変数の中身：'.print_r($_SESSION,true));
        debug('マイページへ遷移します。');
        header('Location:mypage.php');
        debug('▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎');
        debug('▶︎mypageへ遷移してログインページ画面処理終了');
        debug('▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎');
        exit();
        
      }else{
        debug('パスワードがアンマッチです。');
        $err_msg['common']=MSG09;
      }
      
      
      
    }catch(Exception $e){
      error_log('エラー発生'.$e->getMessage());
      $err_msg['common']=MSG07;
    }
  }
}



debug('▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎');
debug('▶︎画面遷移せずログインページ画面処理終了');
debug('▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎');


$siteTitle='ログイン';
require('head.php');

?>



<body>


  <!--メニュー-->
  <?php
  require('header.php');
  ?>

  <p id="js-show-msg"  style="display:none" class="msg-slide">
    <?php echo getSessionFlash('msg_success'); ?>
  </p>

  <!--メインコンテンツ-->
  <div id="contents" class="site-width">

    <!--  メイン-->
    <section id="main-login">
      <div class="form-container">

        <form action="" class="form" method='post'>
          <h2 class="title">ログイン</h2>
          <div class="area-msg">
            <?php echo getErrMsg('common');  ?>
          </div>

          <label class="<?php echo checkErr('email'); ?> ">
            メールアドレス
            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="area-msg">
            <?php  echo getErrMsg('email');  ?>
          </div>

          <label class="<?php echo checkErr('pass'); ?>">
            パスワード
            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          </label>
          <div class="area-msg">
            <?php  echo getErrMsg('pass');  ?>
          </div>

          <label>
            <input type="checkbox" name="pass_save" checked>次回ログインを省略する
          </label>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="ログイン">
          </div>
          パスワードを忘れた方は<a href="passRemindSend.php">コチラ</a>

        </form>

      </div>
    </section>

  </div>

  <?php 
require('footer.php');
?>
