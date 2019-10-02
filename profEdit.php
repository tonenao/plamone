<?php

require('function.php');

debug('=========================================');
debug('=プロフィール編集');
debug('=========================================');
debugLogStart();

//ログイン認証
require('auth.php');

//====================================================================
//プロフ編集画面処理開始
//====================================================================
//DBからユーザーデータを取得
$dbFormData=getUser($_SESSION['user_id']);

debugD($dbFormData,'取得したユーザー情報($dbFormDataの中身)：');

$no_img="img/no-profile-image.png";

if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST送信の内容：'.print_r($_POST,true));
  
  //POSTされた値を変数に代入
  $username=$_POST['username'];
  $tel=$_POST['tel'];
  $zip=$_POST['zip'];
  $addr=$_POST['addr'];
  $age=(!empty($_POST['age']))? $_POST['age'] : 0;
  $email=$_POST['email'];
  $pic=(!empty($_FILES['pic'])) ? upLoadImg($_FILES['pic'],'pic'):"";
  //画像をアップロードしないが、DBにすでに登録されている場合は、DBのパスを変数に格納。
  $pic=(empty($pic) && !empty($dbFormData['pic']))?$dbFormData['pic']:$pic;
  
  //DB登録内容とPOST送信内容に相違があれば各フォームにてバリデーション実施
  if($username !== $dbFormData['username']){
    validMaxLen($username,'username');
  }
  
  if($tel !==$dbFormData['tel']){
    validTel($tel,'tel');
  }
  
  if($zip !== $dbFormData['zip']){
    validZip($zip,'zip');
  }
  
  if($addr !==$dbFormData['addr']){
    validMaxLen($addr,'addr');
  }
  
  if($age !==$dbFormData['age']){
    validNumber($age,'age');
  }
  
  if($email !==$dbFormData['email']){
    validRequired($email,'email');
    validMaxLen($email,'email');
    validEmail($email,'email');
    if(empty($err_msg['email'])){
    validEmailDup($email,'email');
    }
  }
  
  debug('$err_msgの中身：'.print_r($err_msg,true));
  
  if(empty($err_msg)){
    debug('バリデーションOKです。');
    
    try{
      $dbh=dbConnect();
      $sql="UPDATE users SET username=:username,tel=:tel,zip=:zip,addr=:addr,age=:age,email=:email,pic=:pic WHERE id=:user_id ";
      $data=array(':username'=>$username,':tel'=>$tel,':zip'=>$zip,':addr'=>$addr,':age'=>$age,':email'=>$email,':pic'=>$pic,':user_id'=>$dbFormData['id']);
      $stmt=queryPost($dbh,$sql,$data);
      
      if($stmt){
        $_SESSION['msg_success']=SUC02;
        debug('マイページへ遷移します。');
        header("Location:mypage.php");
        exit();
      }

    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common']=MSG07;
    }
    
    
  }
}





$siteTitle="プロフィール編集";
require('head.php');

?>


<body>


  <!--メニュー-->
  <?php
  require('header.php');
  ?>

  <!--メインコンテンツ-->
  <div id="contents" class="site-width">

    <h1 class="page-title">プロフィール編集</h1>

    <!--    サイドバー-->
    <?php require('sidebar.php');?>

    <!--  メイン-->
    <section id="main-index" style="margin-bottom:150px;">
      <div class="form-container" style="border: 5px solid #f1f1f1;">
        <form action="" class="form" method="post" style="width:500px; border:none;"enctype="multipart/form-data">
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>

          <label class="<?php echo checkErr('username'); ?>">
            名前
            <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('username'); ?>
          </div>

          <label class="<?php echo checkErr('tel'); ?>">
            TEl（ハイフンなしで入力ください）
            <input type="text" name="tel" value="<?php echo getFormData('tel'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('tel'); ?>
          </div>

          <label class="<?php echo checkErr('zip'); ?>">
            郵便番号（ハイフンなしで入力ください）
            <input type="text" name="zip" value="<?php echo getFormData('zip'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('zip'); ?>
          </div>

          <label class="<?php echo checkErr('addr'); ?>">
            住所
            <input type="text" name="addr" value="<?php echo getFormData('addr'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('addr'); ?>
          </div>

          <label class="<?php echo checkErr('age'); ?>">
            年齢
            <input type="number" min="0" name="age" value="<?php if(empty(getFormData('age'))){ echo 0;}else{  echo getFormData('age');} ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('age'); ?>
          </div>

          <label class="<?php echo checkErr('email'); ?>">
            Email
            <input type="text" name="email" value="<?php echo getFormData('email') ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('email'); ?>
          </div>

          画像
          <label class="area-drop">
            <!-- <input type="hidden" name="MAX_FILE_SIZE"> -->
            <input type="file" name="pic" class="input-file">
            <img src="<?php if(!empty(getFormData('pic'))){echo getFormData('pic');}else{echo $no_img;} ;?>" alt="" class="prev-img">
          </label>
          <div class="area-msg"><?php echo getErrMsg('pic'); ?></div>

          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="変更する">
          </div>


        </form>

      </div>









    </section>



  </div>

  <?php 
require('footer.php');
?>
