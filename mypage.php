<?php

require('function.php');

debug('=========================================');
debug('=マイページ');
debug('=========================================');
debugLogStart();

require('auth.php');

//================================
// マイページ画面処理
//================================

$u_id=$_SESSION['user_id'];
debug('$u_idの中身：'.$u_id);

$myPageList=getMyPageList($u_id);
debug('$myPageListの中身：'.print_r($myPageList,true));

$myLikeList=getMyLikeList($u_id);
debug('$myLikeListの中身：'.print_r($myLikeList,true));




$siteTitle="マイページ";
require('head.php');


// 

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

    <h1 class="page-title">MyPage</h1>

    <!--    サイドバー-->
    <section id="sidebar">
      <a href="registProduct.php">出品する</a>
      <a href="profedit.php">プロフィール編集</a>
      <a href="passedit.php">パスワード変更</a>
      <a href="withdraw.php">退会</a>
    </section>

    <!--  メイン-->
    <section id="main-index">
      <section id="main-mypage">
        <div class="main-title">
          <div class="search-left">
            <h2>出品作品：<span class="total-num"><?php echo $myPageList['total']?></span>体</h2>
          </div>
        </div>

        <div class="panel-list-mypage">
          <?php foreach($myPageList['data'] as $key=>$val){ ?>
            <a href="registProduct.php?p_id=<?php echo $val['id'] ?>" class="panel-link">
              <div class="panel">
                <div class="thumnail">
                
                  <img src="<?php echo $val['pic1']; ?>" alt="">
                </div>
              </div>
              <div class="panel-info">
                <p>><?php echo $val['name']; ?></p>
                <i class="fa fa-heart"> <span><?php echo $val['count_like']; ?></span></i>
              </div>
            </a>
          <?php } ?>
        </div>

      </section>


      <section id="main-mypage" style="max-height:550px;">
        <div class="main-title">
          <div class="search-left">
            <h2>お気に入り</h2>
          </div>

        </div>

        <div class="like-list-mypage">

        <?php foreach($myLikeList as $key=>$val){ ?>

            
            <a href="productDetail.php?p_id=<?php echo $val['id'] ?>" class="panel-link">
              <div class="panel">
                <div class="thumnail">
                  <img src="<?php echo $val['pic1']; ?>" alt="">
                </div>
              </div>
              <div class="panel-info">
                <p><?php echo $val['name']; ?></p>
                <i class="fa fa-heart"></i>
                <p class="builder"><?php echo $val['username'] ?></p>
              </div>
            </a>

        <?php } ?>


          <!-- <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0004.JPG" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>RX-93 νガンダム</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a>


          <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0005.JPG" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>RX-78GP03D デンドロビウム</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a>



          <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0007.JPG" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>RRX-9/A ナラティブガンダムA装備</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a>



          <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0006.JPG" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>ASW-G-08　ガンダム・バルバトス</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a>



          <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0008.JPG" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>MSZ-006 Zガンダム</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a>



          <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0776.png" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>FA-78 FAガンダム</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a>


          <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0002.JPG" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>RX-0 バンシィ・ノルン</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a>


          <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0002.JPG" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>RX-0 バンシィ・ノルン</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a>


          <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0002.JPG" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>RX-0 バンシィ・ノルン</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a>


          <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0002.JPG" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>RX-0 バンシィ・ノルン</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a>


          <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0002.JPG" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>RX-0 バンシィ・ノルン</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a>


          <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0002.JPG" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>RX-0 バンシィ・ノルン</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a>

          <a href="" class="panel-link">
            <div class="panel">
              <div class="thumnail">
                <img src="img/IMG_0762.png" alt="">
              </div>
            </div>
            <div class="panel-info">
              <p>RX-9/A サザビー</p>
              <i class="fa fa-heart"> <span>2</span></i>
              <p class="builder">ああああ</p>
            </div>
          </a> -->

        </div>



      </section>




    </section>



  </div>

  <?php 
require('footer.php');
?>
