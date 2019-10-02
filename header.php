  <header>
    <div class="site-width">
      <h1><a href="index.php">PLAMONE</a></h1>
      <nav class="top-nav">
        <ul>
          <?php if(empty($_SESSION['user_id'])){ ?>

          <li><a href="signup.php" class="btn btn-primaly">ユーザー登録</a></li>
          <li><a href="login.php">ログイン</a></li>

          <?php }else{ ?>

          <li><a href="mypage.php">マイページ</a></li>
          <li><a href="logout.php">ログアウト</a></li>

          <?php } ?>

        </ul>
      </nav>
    </div>
  </header>
