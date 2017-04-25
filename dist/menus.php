<?php
    ini_set('session.use_only_cookies', true);
    include('connection/connBD.php');
    $mysqli = new mysqli($DBHOST ,$DBUSER, $DBPASS, $DATABASE);
    if ($mysqli -> connect_errno) {	die( "Fallo la conexión a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error); exit;}
    session_start();

    if($tiempo_transcurrido >= 2700 or $acceso==''){  
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
    </head>
    <body>    
        <div class="top-bar-row">
            <div data-equalizer-watch="foo">
                <div class="callout" data-equalizer="bar">
                    <ul class="vertical menu" data-drilldown>
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/ingresoAg.php?f=<?php echo base64_encode(2);?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Agua</strong></a></li>
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/ingresoAg.php?f=<?php echo base64_encode(3);?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Aceite</strong></a></li>
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/ingresoCo.php?f=<?php echo base64_encode(1);?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Combustible</strong></a></li>
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/ingresoHFO.php?c=<?php echo base64_encode('motor');?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>HFO</strong></a></li>   
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/descargas.php?d=<?php echo base64_encode(1);?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Descarga</strong></a></li>
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/ingresoHR.php?c=<?php echo base64_encode('motor');?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Horometro</strong></a></li>
                        <li><a href="javascript:void(0)" onclick="javascript:href='MovFluido/ingresoTK.php?tk=<?php echo base64_encode(1);?>'" target="inicio"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Tk</strong></a></li>
                        <li><a href="javascript:void(0)" target='_parent' onclick="javascript:href='http://192.168.11.73/RegistroDiario/session/closeLog.php'"><i class="fa fa-power-off" aria-hidden="true"></i><strong>Cerrar Sesion</strong></a></li>
                    </ul>
                </div>
            </div>
        </div>
    <script src="assets/js/vendor/jquery-3.1.1.min.js"></script>
    <script src="assets/js/vendor/what-input.js"></script>
    <script src="assets/js/vendor/foundation.min.js"></script>
    <script> $(document).foundation(); </script>
    <script src="assets/js/app.js"></script>
    </body>
</html>
