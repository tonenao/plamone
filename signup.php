<?php


//共有変数・関数ファイルの読み込み
require('function.php');


debug('=========================================');
debug('=ユーザー登録ページ');
debug('=========================================');
debugLogStart();

//================================
// 画面処理
//================================

//POST送信されていた場合
if(!empty($_POST)){
  
  debug('POSTされました！');
  debug('POSTの中身：'.print_r($_POST,true));
  
//変数にユーザー情報を代入
  $email=$_POST['email'];
  $pass=$_POST['pass'];
  $pass_re=$_POST['pass_re'];
  
//未入力チェック
  validRequired($email,'email');
  validRequired($pass,'pass');
  validRequired($pass_re,'pass_re');
  
 if(empty($err_msg)){

   //emailのバリデーション
   validEmail($email,'email');
   validMaxLen($email,'email');
   if(empty($err_msg['email'])){
     validEmailDup($email);
   }
   
   //パスワードのバリデーション
   validHalf($pass,'pass');
   validMaxLen($pass,'pass');
   validMinLen($pass,'pass');
   
   validMatch($pass,$pass_re,'pass_re');
   
debug('err_msgの中身：'.print_r($err_msg,true));
   
   if(empty($err_msg)){
     
        debug('バリデーションOK!');
     
     try{
       $dbh=dbConnect();
       $sql='INSERT INTO users(email,password,login_time,create_date) VALUES(:email,:pass,:login_time,:create_date)';
       $data=array(':email'=>$email,':pass'=>password_hash($pass,PASSWORD_DEFAULT),':login_time'=>date('Y-m-d H:i:s'),':create_date'=>date('Y-m-d H:i:s'));
       $stmt=queryPost($dbh,$sql,$data);
       //クエリ成功の場合
       if($stmt){
         //セッションにログイン情報を詰める
         $sesLimit=60*60;
         $_SESSION['login_limit']=$sesLimit;
         $_SESSION['login_date']=time();
         $_SESSION['user_id']=$dbh->lastInsertId();
         debug('セッション変数の中身:'.print_r($_SESSION,true));
       
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


$siteTitle="サインイン";
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

        <form method="post" action="" class="form">
          <h2 class="title">ユーザー登録</h2>
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>

          <label class="<?php echo checkErr('email'); ?>">
            メールアドレス
            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="area-msg">
            <?php  echo getErrMsg('email');  ?>
          </div>


          <label class="<?php echo checkErr('pass'); ?>">
            パスワード　※英数字6文字以上
            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          </label>
          <div class="area-msg">
            <?php  echo getErrMsg('pass');  ?>
          </div>

          <label class="<?php echo checkErr('pass_re'); ?>">
            パスワード（再入力）
            <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
          </label>
          <div class="area-msg">
            <?php  echo getErrMsg('pass_re');  ?>
          </div>



          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="登録する">
          </div>

        </form>

      </div>
    </section>

  </div>

  <?php 
require('footer.php');
?>
