  <footer>
    Copyright <a href=""> PLAMONE </a>. All Rights Reserved.
  </footer>

  <script src="js/vendor/jquery-3.4.0.min.js"></script>
  <script>
    $(function(){
      //メッセージ表示
      var $jsShowMsg=$('#js-show-msg');
      var msg=$jsShowMsg.text();
      if(msg.replace(/^[\s　]+[\s　]+$/g,"").length){
        $jsShowMsg.slideToggle('slow');
        setTimeout(function(){$jsShowMsg.slideToggle('slow');},5000);
      }

      //画像ライブプレビュー
      var $dropArea=$('.area-drop');
      var $fileInput=$('.input-file');
      $dropArea.on('dragover',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','3px #ccc dashed');
      });

      $dropArea.on('dragleave',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','none');
      });

      $fileInput.on('change',function(e){
        $dropArea.css('border','none');
        var file =this.files[0],
            $img=$(this).siblings('.prev-img'),
            fileReader =new FileReader();
        
        fileReader.onload=function(event){
          $img.attr('src',event.target.result).show();
        };

        fileReader.readAsDataURL(file);

      });  


      


      //テキストエリアカウント
      var $countUp = $('#js-count'),
          $countView =$('#js-count-view');
      $countUp.on('keyup',function(){
        $countView.html($(this).val().length);
      });



      //画像スイッチ
      var $switchImgSub1=$('.js-switch-img-sub1'),
          $switchImgSub2=$('.js-switch-img-sub2'),
          $switchImgMain=$('#js-switch-img-main'),
          imgSub1=$switchImgSub1.attr('src'),
          imgSub2=$switchImgSub2.attr('src'),
          imgMain=$switchImgMain.attr('src');

      $switchImgSub1.on('click',function(e){
          $switchImgSub1.attr('src',imgMain);
          $switchImgMain.attr('src',imgSub1);
          imgSub1=$switchImgSub1.attr('src');
          imgSub2=$switchImgSub2.attr('src');
          imgMain=$switchImgMain.attr('src');
      });    

      $switchImgSub2.on('click',function(e){
          $switchImgSub2.attr('src',imgMain);
          $switchImgMain.attr('src',imgSub2);
          imgSub1=$switchImgSub1.attr('src');
          imgSub2=$switchImgSub2.attr('src');
          imgMain=$switchImgMain.attr('src');
      });

      //お気に入り登録・削除
      var $like,
          $likeCount,
          likeProductId,
          isLogin;
      $like=$('.js-like-click') || null;
      $likeCount=$('#js-like-count') || null;
      likeProductId=$like.data('productid') || null;
      isLogin=$like.data('login');
      console.log(isLogin);

      if(likeProductId !== undefined && likeProductId !== null){
        $like.on('click',function(){
          var $this=$(this);
          $.ajax({
            type:"POST",
            url:"ajaxLike.php",
            data:{ productId:likeProductId}
          }).done(function(data){
            if(isLogin ===1){
              console.log('Ajax Success');
              $this.toggleClass('active');
              $likeCount.html(data);
            }else{
              window.location.href="login.php";
            }
          }).fail(function(msg){
            console.log('Ajax Error');
            conole.log(msg);
          });

        });
      }



    });
  
  </script>



  </body>

  </html>
