<?php
  if($_SERVER["REQUEST_METHOD"] == "POST"){
    var_dump($_FILES);
    if (isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']['error'])) {
      try {
        // $_FILES['upfile']['error'] の値を確認
        switch ($_FILES['upfile']['error']) {
          case UPLOAD_ERR_OK: // OK
              break;
          case UPLOAD_ERR_NO_FILE:
              throw new RuntimeException('No FILE.');
          case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
          case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過
              throw new RuntimeException('Too Big.');
          default:
              throw new RuntimeException('Something wrong...');
        }
        // $_FILES['upfile']['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
        $type = @exif_imagetype($_FILES['upfile']['tmp_name']);
        if (!in_array($type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG), true)) {
          throw new RuntimeException('画像形式が未対応です');
        }
        // 保存する
        $pathData = pathinfo($_FILES['upfile']['name']);
        $now  = date("Y.m.d.His");
        $name = $now.image_type_to_extension($type);
#        $path = sprintf('./uploads/%s%s', $pathData["filename"], image_type_to_extension($type));
        $path = sprintf('./uploads/%s', $name);
        echo $path;
        if (!move_uploaded_file($_FILES['upfile']['tmp_name'], $path)) {
          throw new RuntimeException('ファイル保存時にエラーが発生しました');
        }
        chmod($path, 0644);
      } catch (RuntimeException $e) {
        $msg = array('red', $e->getMessage());
      }
    } 
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0" />
    <title>HTML Media Captureサンプル</title>
    <script>
      window.addEventListener("load", function(){
         
        if (!window.File){
          result.innerHTML = "File API 使用不可";
          return;
        }
         
        document.getElementById("imageFile").addEventListener("change", function(){
          var reader = new FileReader();
           
          reader.onload = function(event){
            document.getElementById("image").src = reader.result;
          }
          var file = document.getElementById("imageFile").files[0];
          reader.readAsDataURL(file);
        }, true);
      }, true);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
  </head>
   
  <body>
    <form enctype="multipart/form-data" method="post" action="">
      <input type="file" name="upfile" accept="image/*;capture=camera" id="imageFile"/>
      <input type="submit" value="送信" />
    </form>
<?php
  if (isset($name)){
?>
    <div id="view-notify-menu" style="float: left;">
      <button v-on:click="sendnotification('yamazaki','<?= $now ?>','<?= $name ?>')" type="button" class="btn btn-default gc-bs-android">山崎さんに通知</button>
      <button v-on:click="sendnotification('all','<?= $now ?>','<?= $name ?>')" type="button" class="btn btn-default gc-bs-android">山崎さんに通知</button>
    </div>
    <script>
    var app = new Vue(
      {
        el: "#view-notify-menu",
        data: {
        },
        methods: {
          sendnotification: function(to,now,filename){
            $.ajax({
              type: "POST",
              url: "send.php",
              data: {
                to: to,
                now: now,
                filename: filename,
              },
              dataType: "json",
            })
          }
        }
      }
    )
    </script>
    <form method="get" action="">
      <input type="submit" value="終了" />
    </form>
<?php
  }
?>
  </body>
  <img id="image">
</html>