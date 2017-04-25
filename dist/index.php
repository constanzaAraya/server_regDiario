<?php
    session_start();
    if($_SESSION['access'] != '' and (isset($_SESSION["ultimoAcceso"]))){
           session_start();
           $_SESSION["ultimoAcceso"]= date("Y-m-d H:i:s"); //se actualiza a medida que se este utilizando el sistema
            setlocale(LC_TIME, "spanish"); 
            $fechaGuardada = $_SESSION["ultimoAcceso"];
            $ahora=date('Y-m-d H:i:s');

           /*echo "<script>function redireccionar(){
            window.location='inicio.php';}
            setTimeout('redireccionar()', 0);</script>";*/
            ?>
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
            <link rel="icon" href="assets/files/img/favicon.png" type="image/png">
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Sistema Registro Diario Ujina</title>

            </head>
            <frameset rows="95,*,0" frameborder="no" border="0" framespacing="0">
            <frame name="nav" src="nav.php" scrolling="no" noresize="noresize"> 
            
            <frame name="inicio" src="inicio.php"> 
            </frameset>
            <frame src="UntitledFrame-74"></frameset>
            
            <noframes>
            <body></body>
            </noframes>
            </html>
    <?php
    /*
    <frameset rows="*" cols="205,*" frameborder="no" border="0" framespacing="0">
    <frame name="menus" src="menus.php" scrolling="auto" style="width:350px; height:300px; float:left;" noresize="noresize">


       else{
           session_destroy();
           echo "<script>function redireccionar(){window.location='../index.html';}
            setTimeout('redireccionar()', 0);</script>";
       }*/
    }
    else{ 
        session_destroy();
        echo "<script>function redireccionar(){window.location='../session/';}
         setTimeout('redireccionar()', 0);</script>";
   }
?>