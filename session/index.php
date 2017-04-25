<?php 
    include('../dist/connection/connBD.php');
    session_start();
    session_destroy();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Session</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="../dist/assets/files/img/favicon.png" type="image/png">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,700" rel="stylesheet" type="text/css">
<link href="../dist/assets/css/main.min.css" rel="stylesheet" type="text/css">
</head>

<body>
   <header class="head">
     <div align="center"><strong><h3>Registro Diario de Fluidos</h3><h7>v. 0.9.3<h7></strong></div>
   </header>
   
   <section class="sec"> 
      <p>
        <div class="row align-justify" align="center">
          <div class="small-8 small-centered medium-4 large-3 columns">
            <form action="login.php" method="post">
               <p><h5>Acceso Sistema Ujina</h5><br>
               <input name="user" type="text" placeholder="Usuario">
               <input name="password" type="password" placeholder="Contraseña"></p>
               <p><input class="button" name="login" type="submit" value="Acceder"></p>
            </form>
            </div>
        </div>
     </p>    
   </section>
    <script src="../dist/assets/js/vendor/jquery-3.1.1.min.js"></script>
    <script src="../dist/assets/js/vendor/what-input.js"></script>
    <script src="../dist/assets/js/vendor/foundation.min.js"></script>
    <script> $(document).foundation(); </script>
    <script src="../dist/assets/js/app.js"></script>
</body>
</html>