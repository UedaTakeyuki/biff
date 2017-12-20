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
  </head>
   
  <body>
    <form>
      <input type="file" accept="image/*;capture=camera" id="imageFile"/>
    </form>
  </body>
  <img id="image">
</html>