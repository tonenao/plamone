<?php

require('function.php');

debug('=========================================');
debug('=ログアウトページ');
debug('=========================================');
debugLogStart();

debug('ログアウトします。');

//セッションを削除(ログアウトする);
session_destroy();

debug('セッション変数の中身：'.print_r($_SESSION,true));

debug('ログインページへ遷移します。');
header('Location:login.php');


?>
