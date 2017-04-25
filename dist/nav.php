<?php //session y conexion a bd
    ini_set('session.use_only_cookies', true);
    include('connection/connBD.php');
    if($tiempo_transcurrido >= 2700 or $acceso=='')//45min-2700
    {  
        session_start(); 
        echo "<script>alert('Su sesion a expirado. Vuelva a iniciar sesion.')</script>";
        echo "<script>window.parent.location.href='../session/closeLog.php';</script>";
        session_destroy(); 
        exit(); 
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="assets/files/img/favicon.png" type="image/png">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,700" rel="stylesheet" type="text/css">
        <link href="assets/css/main.min.css" rel="stylesheet" type="text/css">
        <script>/*
            <!-- desabilita boton derecho del mouse
            function disableselect(e){
            return false
            }
            function reEnable(){
            return true
            }
            //if IE4+
            document.onselectstart=disableselect
            document.oncontextmenu=disableselect*/
        </script>
    </head>    
    <body>
        <header>
          <div class="table-scroll"> 
                <div class="small-12 columns" align="center"><h4><strong>Movimientos de Fluidos, Central Ujina</strong></h4></div>
                <div class="menu-centered">
                    <ul class="menu">
                        <li><a href="javascript:void(0)" onclick="javascript:href='inicio.php'" target="inicio"><strong><i class="fa fa-home" aria-hidden="true"></i></strong></a></li>
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/ingresoAg.php?f=<?php echo base64_encode(2);?>';" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Agua</strong></a></li>
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/ingresoAg.php?f=<?php echo base64_encode(3);?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Aceite</strong></a></li>
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/ingresoCo.php?f=<?php echo base64_encode(1);?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Combustible</strong></a></li>
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/ingresoHFO.php?c=<?php echo base64_encode('motor');?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>HFO</strong></a></li>   
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/descargas.php?d=<?php echo base64_encode(1);?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Descarga</strong></a></li>
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/ingresoHR.php?c=<?php echo base64_encode('motor');?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Horometro</strong></a></li>
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/ingresoTK.php?tk=<?php echo base64_encode(1);?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Tk</strong></a></li>
                        <?php if($acceso!='4'){//4:solo visualiza?><li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/comentario2.php'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Comentarios</strong></a></li><?php }?>
                        <?php if($acceso=='2' or $acceso=='1'){?>
                        <li><a href="javascript:void(0)" onclick="javascript:href='Admin/usuarios.php'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Usuarios</strong></a></li>
                        <?php }?>
                        <li><a href="javascript:void(0)" target='_parent' onclick="javascript:href='../../session/closeLog.php'"><i class="fa fa-power-off" aria-hidden="true"></i><strong></strong></a></li>
                    </ul>
                </div>
          </div>
        </header>
    <script src="assets/js/vendor/jquery-3.1.1.min.js"></script>
    <script src="assets/js/vendor/what-input.js"></script>
    <script src="assets/js/vendor/foundation.min.js"></script>
    <script> $(document).foundation(); </script>
    <script src="assets/js/app.js"></script>
    </body>
</html>