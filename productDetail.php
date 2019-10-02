<?php

require('function.php');

debug('=========================================');
debug('=商品詳細');
debug('=========================================');
debugLogStart();

//================================
// 画面処理
//================================


debug('$_POSTの中身：'.print_r($_POST,true));

$no_img="img/img_no.gif";
$comment="";

//GETパラメータからproductIDを取得
$p_id=(!empty($_GET['p_id']))?$_GET['p_id']:'';
debug('p_idの中身：'.$p_id);

//ログインしている場合はセッション変数からuser_IDを変数に格納。未ログインの場合は0を格納。
$user_id=(!empty($_SESSION['user_id']))? $_SESSION['user_id'] : 0 ;

//DBから取得したproductIDで作品情報を取得
$productData=getProduct($p_id);
debug('$productDataの中身：'.print_r($productData,true));

//DBからメッセージ情報を取得
$messageData=getMessage($p_id);
debug('$messageDataの中身：'.print_r($messageData,true));

//お気に入りの数
$countLike=countLike($p_id);
debug('お気に入りの数：'.$countLike);

debug('isLoginの中身：'.isLogin());


//作品情報が取得できなければindex.phpへ遷移。
if(empty($productData)){
  debug('作品情報がありません。トップページへ遷移します。');
  header('Location:index.php');
  exit();
}



if(!empty($_POST)){
  debug('POST送信があります。');

  //ログインしてるかチェック
  if(isLogin()){

    $comment=$_POST['comment'];

    validrequired($comment,'comment');
    validMaxLen($comment,'comment');

    if(empty($err_msg)){
      debug('バリデーションOK');

      try{

        $dbh=dbConnect();
        $sql="INSERT INTO message (product_id,builder_user,write_user,msg,create_date)VALUES(:p_id, :b_user, :w_user, :msg, :c_date)";
        $data=array(':p_id'=>$p_id, ':b_user'=>$productData['user_id'],':w_user'=>$_SESSION['user_id'],':msg'=>$comment,':c_date'=>date("Y/m/d H:i:s"));
        $stmt=queryPost($dbh,$sql,$data);

        if($stmt){
          $messageData=getMessage($p_id);
        }

      }catch(Exception $e){
        error_log('エラー発生'.$e->getMessage());
        $err_msg['common']=MSG07;

      }
    }
  }else{
    $err_msg['comment']=MSG18;
  }
}



$siteTitle="商品詳細";
require('head.php');

?>


<body>


  <!--メニュー-->
  <?php
  require('header.php');
  ?>


  <!--メインコンテンツ-->
  <div id="contents" class="site-width">

    <h1 class="page-title"><?php echo sanitize($productData['name']); ?></h1>

    <!--    サイドバー-->
    <section id="main-login" style="overflow:hidden;">
      <div class="title">
        <div class="title-left">
        <span class="category"><?php echo sanitize($productData['category']); ?></span>
        </div>
        <div class="title-right">
          <i class="fas fa-heart icon-like js-like-click  <?php if(isLike($p_id,$user_id)){echo 'active';}?>" data-productId="<?php echo sanitize($p_id); ?>" data-login="<?php echo isLogin(); ?>"></i><span id="js-like-count"><?php echo countLike($p_id); ?></span>
        </div>
      </div>
      
      <div class="product-img-container">
        <div class="img-main">
          <img src="<?php if(!empty($productData['pic1'])){echo sanitize($productData['pic1']);}else{echo $no_img;} ;?>" alt="" id="js-switch-img-main">
        </div>
        <div class="img-sub">
          <img src="<?php if(!empty($productData['pic2'])){echo sanitize($productData['pic2']);}else{echo $no_img;} ;?>" alt="" class="js-switch-img-sub1">
          <img src="<?php if(!empty($productData['pic3'])){echo sanitize($productData['pic3']);}else{echo $no_img;} ;?>" alt="" class="js-switch-img-sub2">
        </div>
      </div>

      
      <p style="text-align:right;">ビルダー：<?php echo sanitize($productData['username']); ?></p>
        

      <div style="height:200px; overflow:scroll; border:1px solid #ccc; padding:10px;">
        <p >
        <?php echo $productData['comment']; ?>
        </p>
      </div>
      <div>



          <div class="area-bord">
            <?php if(empty($messageData)){ //投稿メッセージがない場合?>
            <p class="no-msg">投稿されていません。</p>
            <!-- 投稿メッセージがある場合 -->
            <?php }  foreach ($messageData as $key=> $val){ 
              if($val['builder_user']==$val['write_user']){?>
                <div class="msg-cnt msg-left">
                  <div class="avater">
                    <img src="<?php echo sanitize($val['pic']); ?>" alt="">
                    <span><?php echo sanitize($val['username']); ?></span>
                  </div>
                  <p class="msg-inrTxt">
                  <span class="triangle"></span>
                  <?php echo sanitize($val['msg']); ?>
                  </p>
                </div>

              <?php }else{ ?>
                <div class="msg-cnt msg-right">
                  <div class="avater">
                    <img src="<?php echo sanitize($val['pic']); ?>" alt="">
                    <span><?php echo sanitize($val['username']); ?></span>
                  </div>
                  <p class="msg-inrTxt">
                  <span class="triangle"></span>
                  <?php echo sanitize($val['msg']); ?>
                  </p>
                </div>

            <?php }} ?>

            </div>  




            <form action="" class="form-msg" method="post">
 
              <div class="area-msg">
                <?php echo getErrMsg('common');  ?>
              </div>

              <label class="<?php echo checkErr('comment'); ?>">
                コメント
                <textarea name="comment" id="js-count"></textarea>
              </label>

              <div class="area-msg">
                <?php echo getErrMsg('comment'); ?>
              </div>
              
              <div class="btn-container">
                <input type="submit" class="btn btn-mid" value="投稿する" style="float:right;">
              </div>

              
            </form>
            <a href="index.php<?php echo appendGetParam(array('p_id')); ?>" style="display:block; margin-bottom: 150px;">&lt; 作品一覧に戻る</a>

          



      </div>



      

    </section>



  </div>

  <?php 
require('footer.php');
?>
