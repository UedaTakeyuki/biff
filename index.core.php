<?php
  
if($_SERVER["REQUEST_METHOD"] == "POST"){
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
      $path = sprintf('./uploads/%s', $name);
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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Biff</title>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <link rel="stylesheet" href="https://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.css" />
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="https://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>
  </head>
   
  <body>
    <div data-role="page">
      <div data-role="header" data-position="fixed" data-disable-page-zoom="false">
        <h1 style="font-family: 'Parisienne', cursive; text-shadow: 4px 4px 4px #aaa;">Biff</h1>
        <a href="" data-rel="back">戻る</a>
      </div> <!-- header -->

      <div data-role="content">
<?php if (isset($name)): ?>
        <div id="view-notify-menu">
<?php   foreach ($addresses as $key => $value): ?>
          <button data-role="button" data-inline="true" onclick="$(this).button('disable')" v-on:click="sendnotification('<?= $key?>', '<?= $now ?>','<?= $name ?>')" type="button" class="btn btn-default gc-bs-android"><?= $value->name ?>に通知</button>
<?php   endforeach; ?>
          <button data-role="button" data-inline="true" onclick="$(this).button('disable')" v-on:click="sendnotification('zenin', '<?= $now ?>','<?= $name ?>')" type="button" class="btn btn-default gc-bs-android">全員に通知</button>
          <a data-role="button" data-ajax="false" href="./index.php">書類の撮影に戻る</a>
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
                  url: "<?= $sendscript ?>",
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
<?php else: ?>
        <form enctype="multipart/form-data" method="post" action="" data-ajax="false">
          <label for="imageFile">
            ＋カメラを起動
            <input type="file" name="upfile" accept="image/*;capture=camera" id="imageFile" style="display:none;"/>
          </label>
          <img id="image">
          <input type="submit" value="送信" />
        </form>
        <link rel="stylesheet" href="camera_button.css" type="text/css">
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

<?php endif; ?>

      </div><!-- <div data-role="content"> -->

      <div data-role="footer" data-position="fixed" data-disable-page-zoom="false">
        <h4>© Atelier UEDA <img src="favicon.ico"></h4>
      </div>

    </div><!-- <div data-role="page"> -->
  </body>
</html>