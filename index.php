<?php

require('function.php');

debug('=========================================');
debug('=HOME');
debug('=========================================');
debugLogStart();

//================================
// HOME画面処理
//================================

//GETパラメータ（ページ数）の取得
$currentPageNum=(!empty($_GET['p']))? $_GET['p']:1;
//数値以外の値が入っている場合はindex.phpへ遷移
if(empty((int)$currentPageNum)){
  error_log('エラー発生：指定ページに不正な値が入りました');
  header('Location:index.php');
  exit();
}

debug('GETパラメータの中身：'.print_r($_GET,true));
debug('現在のページ：'.$currentPageNum);

$c_id=(!empty($_GET['c_id']))?$_GET['c_id']:"";

$sort=(!empty($_GET['sort']))?$_GET['sort']:0;

//GETパラメータを格納
$getParam="";
if($c_id){$getParam.='&c_id='.$c_id;}
if($sort){$getParam.='&sort='.$sort;}

debug('$getParamの中身：'.$getParam);

//１ページに表示する件数
$listSpan=10;

//カテゴリをDBから取得
$dbCategory=getCategory();
debug('$dbCategoryの中身:'.print_r($dbCategory,true));

//現在の表示レコードの先頭を算出
$currentMinNum=($currentPageNum-1)*$listSpan;

//画面に表示するレコードの情報をDBから取得
$productDataList=getProductList($currentMinNum,$listSpan,$c_id,$sort);
debug('$productDataListの中身：'.print_r($productDataList,true));

//レコード件数
$totalRecord=$productDataList['total'];

//最大ページ数
$totalPageNum=$productDataList['total_page'];

debug('$totalPageNumの中身：'.$totalPageNum);




$siteTitle="HOME";
require('head.php');

?>


<body>


  <!--メニュー-->
  <?php
  require('header.php');
  ?>





  <!--メインコンテンツ-->
  <div id="contents" class="site-width">

    <!--    サイドバー-->
    <section id="sidebar">
      <form>
        <h1 class="title">カテゴリー</h1>
        <div class="selectbox">
          <span></span>

          <select name="c_id">
            <option value="0" selected>指定なし</option>
            <?php foreach($dbCategory as $key =>$val){ ?>
            <option value="<?php echo $val['id'].'"'; if($val['id']==$c_id){echo 'selected';} ?>><?php echo $val['name'] ?></option>
            <?php } ?>

          </select>
        </div>
        <h1 class="title">表示順</h1>
        <div class="selectbox">
          <span></span>
          <select name="sort">
            <option value="0" <?php if($sort==0){echo 'selected';} ?>>指定なし</option>
            <option value="1" <?php if($sort==1){echo 'selected';} ?>>人気が高い順</option>
            <option value="2" <?php if($sort==2){echo 'selected';} ?>>人気が低い順</option>
          </select>
        </div>
        <input type="submit" value="検索">
      </form>
    </section>

    <!--  メイン-->
    <section id="main-index">
      <div class="search-title">
        <div class="search-left">
          <span class="total-num"><?php echo $productDataList['total']; ?></span>件見つかりました。
        </div>
        <div class="search-right">
          <span class="num"><?php echo $currentMinNum+1 ?></span> - <span class="num"><?php if($totalPageNum == $currentPageNum){echo $totalRecord;}else{echo $currentPageNum*$listSpan;} ; ?></span> 件 / <span class="num"><?php echo sanitize($productDataList['total']); ?></span> 件中
        </div>
      </div>

      <div class="panel-list">


      <?php foreach($productDataList['data'] as $key=>$val){ ?>

        <a href="productDetail.php?p_id=<?php echo $val['id'].'&p='.$currentPageNum.$getParam; ?>" class="panel-link">
          <div class="panel">
          <i class="fa fa-heart"> <span class="num"><?php echo sanitize($val['count_like']); ?></span></i>
            <div class="panel-cover"></div>
            <div class="thumnail">
              <img src="<?php echo sanitize($val['pic1']); ?>" alt="">
            </div>
          </div>
          <div class="panel-info">
            <p><?php echo sanitize($val['name']); ?></p>
            <p class="builder"><?php echo sanitize($val['username']); ?></p>
            <!-- <i class="fa fa-heart"> <span><?php echo sanitize($val['count_like']); ?></span></i> -->
          </div>
        </a>
      <?php } ?>


      </div>

      <div class="pagination">
        <ul class="pagination-list">
        <?php 
        $pageColNum=5;
        

        if($currentPageNum==$totalPageNum && $totalPageNum >=$pageColNum){
          $minPageNum=$currentPageNum-4;
          $maxPageNum=$currentPageNum;
        }elseif($currentPageNum==($totalPageNum-1) && $totalPageNum>=$pageColNum){
          $minPageNum=$currentPageNum-3;
          $maxPageNum=$currentPageNum+1;
        }elseif($currentPageNum==2 && $totalPageNum>=$pageColNum){
          $minPageNum=$currentPageNum-1;
          $maxPageNum=$currentPageNum+3;
        }elseif($currentPageNum==1 && $totalPageNum>=$pageColNum){
          $minPageNum=$currentPageNum;
          $maxPageNum=$currentPageNum+4;
        }elseif($totalPageNum<$pageColNum){
          $minPageNum=1;
          $maxPageNum=$totalPageNum;
        }else{
          $minPageNum=$currentPageNum-2;
          $maxPageNum=$currentPageNum+2;
        }

        ?>



          <?php if($currentPageNum !=1): ?>
          <li class="list-item"><a href="?p=1<?php echo $getParam; ?>">&lt;</a></li>
          <?php endif; ?>

          <?php for($i=$minPageNum;$i<=$maxPageNum;$i++): ?>
          <li class="list-item <?php if($currentPageNum==$i) echo "active" ?>"><a href="?p=<?php echo $i.$getParam;?>"><?php echo $i; ?></a></li>
          <?php endfor; ?>

          <?php if($currentPageNum != $totalPageNum): ?>
          <li class="list-item"><a href="?p=<?php echo $totalPageNum.$getParam;?>">&gt;</a></li>
          <?php endif; ?>
        </ul>
      </div>

    </section>

  </div>

  <?php 
require('footer.php');
?>
