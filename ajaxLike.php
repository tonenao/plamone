<?php 
//共通変数・関数ファイルを読み込み
require('function.php');

debug('=========================================');
debug('=　Ajax.php　');
debug('=========================================');
debugLogStart();

//================================
// Ajax処理
//================================

debug('AjaxでPOSTされた内容：'.$_POST['productId']);
$u_id=(isset($_SESSION['user_id']))? $_SESSION['user_id']: "" ;

  if(isset($_POST['productId']) && isset($u_id) && isLogin()){
    debug('POST送信があります。');
    $p_id=$_POST['productId'];
    debug('商品ID：'.$p_id);

    try{
      $dbh=dbConnect();

        if(isLike($p_id,$u_id)){ 
          $sql="DELETE FROM p_like WHERE product_id=:p_id AND user_id=:u_id";
          $data=array(':p_id'=>$p_id,':u_id'=>$u_id);
          $stmt=queryPost($dbh,$sql,$data);
          echo countLike($p_id);

        }else{
          $sql="INSERT INTO p_like (product_id,user_id,create_date) VALUES (:p_id,:u_id,:create_date)";
          $data=array(':p_id'=>$p_id,':u_id'=>$u_id,':create_date'=>date('Y-m-d H:i:s'));
          $stmt=queryPost($dbh,$sql,$data);
          echo countLike($p_id);
        }
          
    }catch(Exception $e){
      error_log('エラー発生'.$e->getMessage());
    }

    debug('Ajax処理終了。');

  }
// }


?>