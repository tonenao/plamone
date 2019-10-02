<?php

require('function.php');

debug('=========================================');
debug('=退会ページ');
debug('=========================================');
debugLogStart();

//ログイン認証
require('auth.php');


//================================
// ログイン画面処理
//================================

//POSTされていた場合
if(!empty($_POST)){
  debug('POST送信があります。');
  
  //例外処理
  try{
    $dbh=dbConnect();
    $sql1='UPDATE users SET delete_flg=1  WHERE id = :user_id';
    $sql2='UPDATE product SET delete_flg = 1 WHERE user_id = :user_id';
    $sql3='UPDATE p_like SET delete_flg = 1 WHERE user_id = :user_id';
    $data=array(':user_id'=>$_SESSION['user_id']);
    $stmt1=queryPost($dbh,$sql1,$data);
    $stmt2=queryPost($dbh,$sql2,$data);
    $stmt3=queryPost($dbh,$sql3,$data);
    
    if($stmt1){
      //セッション削除
      session_destroy();
      debug('セッション変数の中身：'.print_r($_SESSION,true));
      debug('トップページへ遷移します。');
      header('Location:index.php');
    }else{
      debug('クエリが失敗しました。');
      $err_msg['common']=MSG07;
    }
    
    
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common']=MSG07;
  }
  
  
}



debug('▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎');
debug('▶︎退会画面処理終了');
debug('▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎');




$siteTitle="退会";
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
      <div class="form-container" style="text-align:center;">

        <form action="" class="form" method="post">
          <h2 class="title">退会</h2>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="退会する" style="float:none;" name="submit">
          </div>
        </form>

      </div>
      <a href="mypage.php">&lt; マイページに戻る</a>
    </section>

  </div>

  <?php 
require('footer.php');
?>
