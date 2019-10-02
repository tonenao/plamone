<?php

require('function.php');

debug('=========================================');
debug('=作品出品登録');
debug('=========================================');
debugLogStart();

//GETデータを格納
$p_id=(!empty($_GET['p_id']))? $_GET['p_id'] :"";
debug('p_idの中身：'.print_r($p_id,true));

$no_img="img/img_no.gif";

//カテゴリーデータをDBから取得
$dbCategory=getCategory();
debug('$dbCategoryの中身：'.print_r($dbCategory,true));
//商品データをDBから取得
$dbFormData=getProduct($p_id);
debug('$dbFormDataの中身：'.print_r($dbFormData,true));
//新規登録か既存編集画面か判別用フラグ
$edit_flg=(empty($dbFormData))?false:true;
debug('$edit_flgの中身：'.print_r($edit_flg,true));

//商品データがあるが、登録されているuser_idと、ログインしているuser_idが異なる場合はマイページへ遷移する。（画面編集できるのは、登録したユーザーのみ）
if(!empty($dbFormData) && $dbFormData['user_id'] !== $_SESSION['user_id'] ){
  debug('作品データとログイン情報が一致しません。mypageに遷移します。');
  header("Location:mypage.php");
  exit();
}

//POST送信あり
if(!empty($_POST)){
  debug('POST送信があります。');

  debug('POSTの中身：'.print_r($_POST,true));
  debug('FILEの中身：'.print_r($_FILES,true));

  //各フォームの値を変数に格納
  
  $name=$_POST['name'];
  $comment=$_POST['comment'];
  $category_id=$_POST['category_id'];
  //画像をアップロードされていたら、変数をパスを格納
  $pic1=(!empty($_FILES['pic1'])) ? upLoadImg($_FILES['pic1'],'pic1'):"";
  //画像をアップロードしないが、DBにすでに登録されている場合は、DBのパスを変数に格納。
  $pic1=(empty($pic1) && !empty($dbFormData['pic1']))?$dbFormData['pic1']:$pic1;
  $pic2=(!empty($_FILES['pic2'])) ? upLoadImg($_FILES['pic2'],'pic2'):"";
  $pic2=(empty($pic2) && !empty($dbFormData['pic2']))?$dbFormData['pic2']:$pic2;
  $pic3=(!empty($_FILES['pic3'])) ? upLoadImg($_FILES['pic3'],'pic3'):"";
  $pic3=(empty($pic3) && !empty($dbFormData['pic3']))?$dbFormData['pic3']:$pic3;
 

  debug('$err_msgの中身：'.print_r($err_msg,true));
  debug('pic1の中身：'.$pic1);
  debug('pic2の中身：'.$pic2);
  debug('pic3の中身：'.$pic3);

  //バリデーションチェック

  //新規登録の場合
  if(!$edit_flg){

    validRequired($name,'name');
    validMaxLen($name,'name');
    validMaxLen($comment,'comment',500);
    if(empty($_POST['category_id'])){
      $err_msg['category_id']="選択されていません。";
      debug('カテゴリーが選択されていません。');
    }

  //既存編集画面の場合  
  }else{
    if($dbFormData['name'] !== $_POST['name']){
      validRequired($name,'name');
      validMaxLen($name,'name');
    }
    if($dbFormData['comment'] !== $_POST['comment']){
      validMaxLen($comment,'comment',500);
    }
    if(empty($_POST['category_id'])){
      $err_msg['category_id']="選択されていません。";
      debug('カテゴリーが選択されていません。');
    }
  }

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    try{
      $dbh=dbConnect();

      if($edit_flg){
        $sql="UPDATE product SET name=:name,category_id=:category_id,comment=:comment,pic1=:pic1,pic2=:pic2,pic3=:pic3 WHERE id=:p_id";
        $data=array(':name'=>$name,':category_id'=>$category_id,':comment'=>$comment,':pic1'=>$pic1,':pic2'=>$pic2,':pic3'=>$pic3,':p_id'=>$p_id);
      }else{
        $sql="INSERT INTO product (name,category_id,comment,pic1,pic2,pic3,user_id,create_date) VALUES(:name,:category_id,:comment,:pic1,:pic2,:pic3,:user_id,:create_date)";
        $data=array(':name'=>$name,':category_id'=>$category_id,':comment'=>$comment,':pic1'=>$pic1,':pic2'=>$pic2,':pic3'=>$pic3,':user_id'=>$_SESSION['user_id'],':create_date'=>date('Y-m-d H:i:s'));        
      }
      debug('SQL:'.$sql);
      debug('流し込みデータ：'.print_r($data,true));
      $stmt=queryPost($dbh,$sql,$data);
      if($stmt){
        $_SESSION['msg_success']=SUC04;
        debug('更新成功。マイページへ遷移します。');
        header("Location:mypage.php");
      }

    }catch(Exception $e){
      error_log('エラー発生'.$e->getMessage());
      $err_msg['common']=MSG07;
    }
  }

}
debug('▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎');
debug('▶︎作品編集画面表示処理終了');
debug('▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎▶︎');




$siteTitle=($edit_flg)?"作品編集":"作品登録";
require('head.php');

?>


<body>


  <!--メニュー-->
  <?php
  require('header.php');
  ?>

  <!--メインコンテンツ-->
  <div id="contents" class="site-width">

    <h1 class="page-title"><?php echo ($edit_flg)?'作品を編集する':'作品を出品する'; ?></h1>


    <!--    サイドバー-->
    <?php require('sidebar.php'); ?>

    <!--  メイン-->
    <section id="main-index">
      <div class="form-container" style="border: 5px solid #f1f1f1; margin-bottom:200px;">
        <form action="" class="form" method="post" style="width:500px; border:none;" enctype="multipart/form-data">
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>


          <label class="<?php echo checkErr('name'); ?> ">
            作品名
            <input type="text" name="name" value="<?php echo getFormData('name');?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('name'); ?>
          </div>

          <label for="">
            カテゴリー
            <select name="category_id" id="">
            <option value="0" <?php if(getFormData('category_id')==0){ echo 'selected'; } ?> >選択してください。</option>
              
              <?php  
                foreach($dbCategory as $key=>$val){
              ?>
              <option value="<?php echo $val['id']; ?>"<?php if(getFormData('category_id')==$val['id']) echo 'selected' ?>><?php echo $val['name']; ?></option>
              <?php  
                }
              ?>
            </select>
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('category_id'); ?>
          </div>

          <label class="<?php echo checkErr('comment'); ?> ">
            詳細
            <textarea name="comment" id="js-count"><?php echo getFormData('comment');?></textarea>
          </label>
          <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
          <div class="area-msg">
            <?php echo getErrMsg('comment'); ?>
          </div>


          画像１
          <label class="area-drop">
            <!-- <input type="hidden" name="MAX_FILE_SIZE"> -->
            <input type="file" name="pic1" class="input-file">
            <img src="<?php if(!empty(getFormData('pic1'))){echo getFormData('pic1');}else{echo $no_img;} ;?>" alt="" class="prev-img">
          </label>
          <div class="area-msg"><?php echo getErrMsg('pic1'); ?></div>

          画像２
          <label class="area-drop">
            <!-- <input type="hidden" name="MAX_FILE_SIZE"> -->
            <input type="file" name="pic2" class="input-file">
            <img src="<?php if(!empty(getFormData('pic2'))){echo getFormData('pic2');}else{echo $no_img;} ;?>" alt="" class="prev-img">
          </label>
          <div class="area-msg"><?php echo getErrMsg('pic2'); ?></div>

          画像３
          <label class="area-drop">
            <!-- <input type="hidden" name="MAX_FILE_SIZE"> -->
            <input type="file" name="pic3" class="input-file">
            <img src="<?php if(!empty(getFormData('pic3'))){echo getFormData('pic3');}else{echo $no_img;} ;?>" alt="" class="prev-img">
          </label>
          <div class="area-msg"><?php echo getErrMsg('pic3'); ?></div>

          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="<?php echo ($edit_flg)?'保存する':'出品する'; ?>">
          </div>


        </form>

      </div>









    </section>



  </div>

  <?php 
require('footer.php');
?>
