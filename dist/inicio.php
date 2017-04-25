<?php
    ini_set('session.use_only_cookies', true);
    include('connection/connBD.php');
    session_start();
    
    if($tiempo_transcurrido >= 2700 or $acceso==''){  
        session_start(); 
        echo "<script>alert('Su sesion a expirado. Vuelva a iniciar sesion.')</script>";
        echo "<script>window.parent.location.href='../session/closeLog.php';</script>";
        session_destroy(); 
        exit(); 
    }

    if($_POST['fecha_datos']!=''){
        $_SESSION['dato']=$_POST['fecha_datos'];
        $datoss = explode("-",$_POST['fecha_datos']);
        $años=$datoss[0];
        $mess=$datoss[1];
    }else{
        if($dia=='01'){
            $fecha_=$mysqli->query("SELECT DISTINCT DATE_FORMAT(now(),'%m') as month, year(now()) as year FROM tb_MovFluidos");
            $f=$row_fechas=$fecha_->fetch_assoc();
            $_SESSION['dato']=$f['year'].'-'.$f['month'];
            $años=$f['year'];
            $mess=$f['month'];
            $mess=date("m", strtotime( "-1 month", strtotime($año.'-'.$mess)));
            $años=date("Y", strtotime( "-1 month", strtotime($año.'-'.$mess)));
        }else{
            $fecha_=$mysqli->query("SELECT DISTINCT DATE_FORMAT(now(),'%m') as month, year(now()) as year FROM tb_MovFluidos");
            $f=$row_fechas=$fecha_->fetch_assoc();
            $_SESSION['dato']=$f['year'].'-'.$f['month'];
            $años=$f['year'];
            $mess=$f['month'];
        }
    }
    $dat=$mysqli->query("SELECT count(*) as cantidad FROM tb_MovFluidos WHERE month(fecha)=DATE_FORMAT(now(),'%m') and year(fecha)=DATE_FORMAT(now(),'%Y')");
    $d=$dat->fetch_assoc();
    $ok=$d['cantidad'];
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
        <style>/*#tit{color:#dd5;} #backgr1{background-color:#21857C;} a4a4a4  26a69a f2f2f2*/
            #tit{color:#3A3C58;}
            #backgr1{background-color:#F2F2F2;}
            #backgr2{background-color:#fff;}
        </style>
    </head>
    <body>
        <?php $fechas=$mysqli->query("SELECT DISTINCT DATE_FORMAT(fecha,'%m') as month, year(fecha) as year FROM tb_MovFluidos ORDER BY fecha ASC");
        if($_POST['fecha_datos']!=''){ $año_mes=$_POST['fecha_datos']; }
        else{ $año_mes=$años.'-'.$mess; }?>
        <div class="row collapse"><div class="small-12 columns">
            <?php /*<fieldset class="fieldset">*/?>
                <form method="post">
                    <select name="fecha_datos" onchange="submit()"><option value="" selected>Selecciona Periodo</option>
                        <?php while($row_fechas=$fechas->fetch_assoc()){?>
                        <option value="<?php echo $row_fechas['year'].'-'.$row_fechas['month'];?>" 
                        <?php if($row_fechas['year'].'-'.$row_fechas['month']==$_POST['fecha_datos'] or $row_fechas['year'].'-'.$row_fechas['month']==$años.'-'.$mess){echo "selected";}?>>
                        <?php echo $row_fechas['year'].'-'.$row_fechas['month'];?></option>
                        <?php }?>
                        <?php if($dia!='01' and $ok=='0'){?><option value="<?php echo date('Y-m');?>"><?php echo date('Y-m');?></option><?php }?>
                    </select>
                </form>
            <?php /*</fieldset>*/?>
        </div></div>
<?php echo '<div align="center"><strong>Período: '.$mess.'-'.$años.'</strong></div>';?><br>

<?php $fluidos_=$mysqli->query("SELECT tbl_Tk.idTk as idF, tbl_Fluidos.nombre as nameF, tbl_TipoFluidos.nombre as tipoF  FROM tbl_Tk JOIN tbl_Fluidos ON tbl_Tk.id_Fluido=tbl_Fluidos.idFluido JOIN tbl_TipoFluidos ON tbl_Fluidos.id_TipoFluido=tbl_TipoFluidos.idTipoFluido WHERE ingreso='0'");
    while($row_fluidos=$fluidos_->fetch_assoc()){
        if($row_fluidos['tipoF']=='Agua'){
            $aguas=$mysqli->query("SELECT litro, saldoActual FROM (
            SELECT (SELECT litros FROM tb_MovFluidos WHERE id_Tk='".$row_fluidos['idF']."' AND month(fecha)='$mess' AND year(fecha)='$años' AND litros > 0 group BY id_Tk) AS litro,
            (SELECT round(nivelTk*(tbl_Tk.capacidad*1000)) FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='".$row_fluidos['idF']."' AND month(fecha)='$mess' AND year(fecha)='$años' AND nivelTk <= 0 order by fecha DESC limit 1) AS saldoActual
            ) AS datos");
            $row_aguas=$aguas->fetch_assoc();
            $litros[]=$row_aguas['litro'];
            $saldoActuales[]=$row_aguas['saldoActual'];
            $dato_sum=0;
            for($x=1;$x<31;$x++){
                $sumas_consumos=$mysqli->query("SELECT round(consumoAnterior-(nivelConsumo*capacidad)) AS consumo FROM (
                SELECT (tbl_Tk.capacidad*1000) as capacidad,
                (SELECT nivelTk FROM tb_MovFluidos WHERE id_Tk='".$row_fluidos['idF']."' AND fecha='$años-$mess-$x' AND nivelTk <= 0) AS nivelConsumo,
                (SELECT (nivelTk*(tbl_Tk.capacidad*1000)) FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='".$row_fluidos['idF']."' AND fecha=(select DATE_SUB('$años-$mess-$x', INTERVAL 1 DAY)) LIMIT 1) AS consumoAnterior
                FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk 
                WHERE id_Tk='".$row_fluidos['idF']."' AND fecha='$años-$mess-$x' LIMIT 1
                )AS datos");
                $row_sumas=$sumas_consumos->fetch_assoc();
                $dato_sum=$dato_sum-$row_sumas['consumo'];
            }
            $names[]=$row_fluidos['nameF'];
            $consumos[]=$dato_sum;
        }
        if($row_fluidos['tipoF']=='Aceite'){ $cant=$cant+1;
            $aceites=$mysqli->query("SELECT litro, saldoActual FROM (
            SELECT (SELECT litros FROM tb_MovFluidos WHERE id_Tk='".$row_fluidos['idF']."' AND month(fecha)='$mess' AND year(fecha)='$años' AND litros > 0 group BY id_Tk) AS litro,
            (SELECT round(nivelTk*(tbl_Tk.capacidad*1000)) FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='".$row_fluidos['idF']."' AND month(fecha)='$mess' AND year(fecha)='$años' AND nivelTk <= 0 order by fecha DESC limit 1) AS saldoActual
            ) AS datos");
            $row_aceites=$aceites->fetch_assoc();
            $litros2[]=$row_aceites['litro'];
            $saldoActuales2[]=$row_aceites['saldoActual'];
            $dato_sum2=0;
            for($x=1;$x<=31;$x++){
                $sumas_consumos2=$mysqli->query("SELECT round((nivelConsumo*capacidad)-consumoAnterior) AS consumo FROM (
                SELECT (tbl_Tk.capacidad*1000) as capacidad,
                (SELECT nivelTk FROM tb_MovFluidos WHERE id_Tk='".$row_fluidos['idF']."' AND fecha='$años-$mess-$x' AND nivelTk <= 0) AS nivelConsumo,
                (SELECT (nivelTk*(tbl_Tk.capacidad*1000)) FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='".$row_fluidos['idF']."' AND fecha=(select DATE_SUB('$años-$mess-$x', INTERVAL 1 DAY)) LIMIT 1) AS consumoAnterior
                FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk 
                WHERE id_Tk='".$row_fluidos['idF']."' AND fecha='$años-$mess-$x' LIMIT 1
                )AS datos");
                $row_sumas2=$sumas_consumos2->fetch_assoc();
                $dato_sum2=$dato_sum2 - $row_sumas2['consumo'];
            }
            $names2[]=$row_fluidos['nameF'];
            $consumos2[]=$dato_sum2;
        }
        if($row_fluidos['tipoF']=='Combustible'){
            $sql = "SELECT Total_descargado_ltsDiesel, Total_descargado_ltsHFO, consumo_mDiesel, consumo_mHfo, total_desc_diesel, total_desc_hfo, cant_camionesDiesel, cant_camionesHFO,consumo_caldera, consumo_motores, consumo_caldera-consumo_motores AS consumo_mensual FROM (
                SELECT 
                    (SELECT sum(round(lecturaFin-lecturaIni)) FROM tb_MovFluidos WHERE month(fecha)='$mess' AND year(fecha)='$años' AND id_Tk='16' AND lecturaIni > 0) AS Total_descargado_ltsDiesel,
                    (SELECT round(sum(round(lecturaFin-lecturaIni,2)*1000)) FROM tb_MovFluidos WHERE month(fecha)='$mess' AND year(fecha)='$años' AND id_Tk='17' AND lecturaIni > 0) AS Total_descargado_ltsHFO,
                    (SELECT COUNT(lecturaIni) FROM tb_MovFluidos WHERE month(fecha)='$mess' AND year(fecha)='$años' AND id_Tk='16' AND lecturaIni>0 GROUP BY id_Tk) AS cant_camionesDiesel,
                    (SELECT COUNT(lecturaIni) FROM tb_MovFluidos WHERE month(fecha)='$mess' AND year(fecha)='$años' AND id_Tk='17' AND lecturaIni>0 GROUP BY id_Tk) AS cant_camionesHFO,
                    (SELECT sum(kg_lt_camion) FROM tb_MovFluidos WHERE month(fecha)='$mess' AND year(fecha)='$años' AND id_Tk='16') AS total_desc_diesel,
                    (SELECT sum(kg_lt_camion) FROM tb_MovFluidos WHERE month(fecha)='$mess' AND year(fecha)='$años' AND id_Tk='17') AS total_desc_hfo,
                    (SELECT round(sum(lecturaFin-lecturaIni)) FROM tb_MovFluidos WHERE id_Tk='16' AND month(fecha)='$mess' AND year(fecha)='$años' AND lecturaIni < 0) AS consumo_mDiesel,
                    (SELECT round(sum(lecturaFin-lecturaIni)) FROM tb_MovFluidos WHERE id_Tk='17' AND month(fecha)='$mess' AND year(fecha)='$años' AND lecturaIni < 0) AS consumo_mHfo,
                    (SELECT round(sum(litros)) FROM tb_MovFluidos WHERE litros <> '' AND id_Componente='9' AND month(fecha)='$mess' AND year(fecha)='$años') AS consumo_caldera,
                    (SELECT round(sum(lecturaFin-lecturaIni)) AS consumoMotor1 FROM tb_MovFluidos WHERE id_Tk='16' AND month(fecha)='$mess' AND year(fecha)='$años' AND lecturaIni < 0) AS consumo_motores
                FROM tb_MovFluidos LIMIT 1
            )AS datos";
        }
        $dat=$mysqli->query($sql);
        $row_dat=$dat->fetch_assoc();
    }

    $horas=$mysqli->query("SELECT sum(hr_fin-hr_ini) AS Hrs FROM tbl_HrsOperacion WHERE month(fecha)='$mess' AND year(fecha)='$años' AND id_Componente IN (1,2,3,4,5,6) group BY id_Componente");
?>        
        <div class="row tabs-content" id="backgr1">
            <div class="small-12 columns">
                <div class="row" id="tit">
                    <div class="small-2 columns"><strong>COMBUSTIBLES</strong></div>
                    <div class="small-2 columns"><strong>Total Descargados</strong></div>
                    <div class="small-3 columns"><strong>Consumo por Motores</strong></div>
                    <div class="small-2 columns"><strong>Total Descargado</strong></div>
                    <div class="small-3 columns"><strong>Camiones Recibidos</strong></div>
                </div>
                <div class="row" id="backgr2">
                    <div class="small-2 columns"><strong>Diesel</strong></div>
                    <div class="small-2 columns"><?php echo str_replace("-","",number_format($row_dat['Total_descargado_ltsDiesel'],0,',','.'));?> Lts.</div>
                    <div class="small-3 columns"><?php echo str_replace("-","",number_format($row_dat['consumo_mDiesel'],0,',','.'));?> Lts.</div>
                    <div class="small-2 columns"><?php echo str_replace("-","",number_format($row_dat['total_desc_diesel'],0,',','.'));?> kgs.</div>
                    <div class="small-3 columns"><?php echo $row_dat['cant_camionesDiesel'];?></div>
                </div>
                <div class="row" id="backgr2">
                    <div class="small-2 columns"><strong>HFO</strong></div>
                    <div class="small-2 columns"><?php echo str_replace("-","",number_format($row_dat['Total_descargado_ltsHFO'],0,',','.'));?> Lts.</div>
                    <div class="small-3 columns"><?php echo str_replace("-","",number_format($row_dat['consumo_mHfo'],0,',','.'));?> Lts.</div>
                    <div class="small-2 columns"><?php echo str_replace("-","",number_format($row_dat['total_desc_hfo'],0,',','.'));?> kgs.</div>
                    <div class="small-3 columns"><?php echo $row_dat['cant_camionesHFO'];?></div>
                </div>
            </div>
        </div>
        <div class="row tabs-content">
            <div class="small-12 columns">
                <div class="row" id="backgr2">
                    <div class="small-3 columns"><strong>Consumo Caldera Aux Diesel:</strong></div>
                    <div class="small-9 columns"><?php echo str_replace("-","",number_format($row_dat['consumo_caldera'],0,',','.'));?> Lts.</div>
                </div>
                <div class="row" id="backgr2">
                    <div class="small-3 columns"><strong>Consumo Motores Diesel:</strong></div>
                    <div class="small-9 columns"><?php echo str_replace("-","",number_format($row_dat['consumo_motores'],0,',','.'));?> Lts.</div>
                </div>
                <div class="row" id="backgr2">
                    <div class="small-3 columns"><strong>Consumo Mensual Diesel:</strong></div>
                    <div class="small-9 columns"><?php echo str_replace("-","",number_format($row_dat['consumo_mensual'],0,',','.'));?> Lts.</div>
                </div>
            </div>
        </div>
        <br>

        <div class="row tabs-content" id="backgr1">
            <div class="small-3 columns"><div  id="tit"><strong>AGUAS</strong></div>
                <?php foreach($names as $nombre){?>
                <div class="row" id="backgr2">
                    <div class="small-3 columns"><strong><?php echo $nombre;?></strong></div>
                </div>
                <?php }?>
            </div>
            <div class="small-3 columns"><div id="tit"><strong>Total Descargado</strong></div>
                <?php foreach($litros as $lts){?>
                <div class="row" id="backgr2">
                    <div class="small-3 columns"><?php echo str_replace("-","",number_format($lts,0,',','.'));?></div>
                </div>
                <?php }?>
            </div>
            <div class="small-3 columns"><div id="tit"><strong>Consumo</strong></div>
                <?php foreach($consumos as $consumo){?>
                <div class="row" id="backgr2">
                    <div class="small-3 columns"><?php echo str_replace("-","",number_format($consumo,0,',','.'));?></div>
                </div>
                <?php }?>
            </div>
            <div class="small-3 columns"><div id="tit"><strong>Saldo Actual</strong></div>
                <?php foreach($saldoActuales as $saldoAct){?>
                <div class="row" id="backgr2">
                    <div class="small-3 columns"><?php echo str_replace("-","",number_format($saldoAct,0,',','.'));?></div>
                </div>
                <?php }?>
            </div>
        </div>
        <br>
        
         <div class="row tabs-content" id="backgr1">
            <div class="small-3 columns"><div  id="tit"><strong>ACEITES</strong></div>
                <?php foreach($names2 as $nombre2){?>
                <div class="row" id="backgr2">
                    <div class="small-3 columns"><strong><?php echo $nombre2;?></strong></div>
                </div>
                <?php }?>
            </div>
            <div class="small-3 columns"><div id="tit"><strong>Total Descargado</strong></div>
                <?php foreach($litros2 as $lts2){?>
                <div class="row" id="backgr2">
                    <div class="small-3 columns"><?php echo str_replace("-","",number_format($lts2,0,',','.'));?></div>
                </div>
                <?php }?>
            </div>
            <div class="small-3 columns"><div id="tit"><strong>Consumo</strong></div>
                <?php foreach($consumos2 as $consumo2){?>
                <div class="row" id="backgr2">
                    <div class="small-3 columns"><?php echo str_replace("-","",number_format($consumo2,0,',','.'));?></div>
                </div>
                <?php }?>
            </div>
            <div class="small-3 columns"><div id="tit"><strong>Saldo Actual</strong></div>
                <?php foreach($saldoActuales2 as $saldoAct2){?>
                <div class="row" id="backgr2">
                    <div class="small-3 columns"><?php echo str_replace("-","",number_format($saldoAct2,0,',','.'));?></div>
                </div>
                <?php }?>
            </div>
        </div>
        <br>

        <?php while($row_horas=$horas->fetch_assoc()){$rows[]=$row_horas['Hrs'];} $x=0; ?>
        <div class="row tabs-content">
            <div class="small-12 columns" align="center" id="backgr1"><div id="tit"><strong>HORAS DE OPERACIÓN</strong></div></div>
            <?php foreach($rows as $row_horas){ ?>
                <div class="small-2 columns"><div><strong>Motor <?php echo $x+1;?></strong></div>
                    <div class="row" id="backgr2">
                        <div class="small-3 columns"><?php echo $row_horas;?></div>
                    </div>
                </div> 
            <?php $x=$x+1; }?>
        </div>
        <br>

        <script src="assets/js/vendor/jquery-3.1.1.min.js"></script>
        <script src="assets/js/vendor/what-input.js"></script>
        <script src="assets/js/vendor/foundation.min.js"></script>
        <script> $(document).foundation(); </script>
        <script src="assets/js/app.js"></script>
<?php /*
    <?php
        ini_set('session.use_only_cookies', true);
        include('connection/connBD.php');
        $mysqli = new mysqli($DBHOST ,$DBUSER, $DBPASS, $DATABASE);
        if ($mysqli -> connect_errno) {	die( "Fallo la conexión a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error); exit;}
        session_start();
        
        if($_SESSION['access'] != 'ok'){
            session_start();	
            header ("Location: index.php");
            session_destroy(); 
            exit;
        }
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <title></title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" href="assets/files/img/favicon.png" type="image/png">
            <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,700" rel="stylesheet" type="text/css">
            <link href="assets/css/main.min.css" rel="stylesheet" type="text/css">
        </head>
        <body>
            <header class="head">
                <div class='row'>
                    <div class="small-12 large-12 columns" align='center'><h3>Movimientos Diarios Central Ujina</h3></div>                
                </div>        
                <div class="top-bar-row" align='center'>
                    <ul class="dropdown menu" data-dropdown-menu>
                        <li><a href="MovFluido/ingresoAg.php?f=<?php echo base64_encode(2);?>"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Agua</strong></a></li>
                        <li><a href="MovFluido/ingresoAg.php?f=<?php echo base64_encode(3);?>"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Aceite</strong></a></li>
                        <li><a href="MovFluido/ingresoCo.php?f=<?php echo base64_encode(1);?>"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Combustible</strong></a></li>
                        <li><a href="MovFluido/ingresoHFO.php?c=<?php echo base64_encode('motor');?>"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>HFO</strong></a></li>   
                        <li><a href="MovFluido/descargas.php?d=<?php echo base64_encode(1);?>"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Descarga</strong></a></li>
                        <li><a href="MovFluido/ingresoHR.php?c=<?php echo base64_encode('motor');?>"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Horas</strong></a></li>
                        <li><a href="MovFluido/ingresoTK.php?tk=<?php echo base64_encode(1);?>"><strong><i class="fa fa-caret-right" aria-hidden="true"></i>Tk</strong></a></li>
                        <li><a href="http://192.168.11.73/RegistroDiario/session/closeLog.php"><span data-tooltip aria-haspopup="true" class="has-tip top" data-disable-hover="false" tabindex="2" title='Cerrar Sesion'><i class="fa fa-power-off" aria-hidden="true"></i></span></a></li>
                    </ul>
                </div>
            </header><p></p>

            <div class="small-11 small-centered large-11 columns">
            <?php $sql = "SELECT day(fecha) AS Dia, tbl_Fluidos.nombre AS Fluido, 
                tb_MovFluidos.Lt_recibido AS ConsumoHFO, 
                tb_MovFluidos.hrOpIni AS HrsOpHFO, 
                round((tb_MovFluidos.nivelTk*100),2) AS NivelTK, 
                (tb_MovFluidos.lecturaFin-tb_MovFluidos.lecturaIni) AS ConsumoDiesel, 
                tb_MovFluidos.consumoCaldera AS ConsumoCaldera, 
                tb_MovFluidos.stock Stock, 
                ((tb_MovFluidos.lecturaFin-tb_MovFluidos.lecturaIni)+consumoCaldera) AS ConstumoTotal
                FROM tb_MovFluidos, tbl_Tk, tbl_Fluidos
                WHERE tb_MovFluidos.id_Tk=tbl_Tk.idTk
                    AND tbl_Tk.id_Fluido=tbl_Fluidos.idFluido
                    AND tbl_Tk.ingreso='0'
                    AND tb_MovFluidos.consumo_descarga='c'
                    AND month(fecha)='01' AND year(fecha)='2017'
                    AND 
                    CASE tbl_Fluidos.idFluido
                    WHEN '1' THEN tb_MovFluidos.lecturaFin AND tb_MovFluidos.lecturaIni
                    WHEN '2' THEN tb_MovFluidos.Lt_recibido    
                    WHEN '3' THEN tb_MovFluidos.nivelTk
                    WHEN '4' THEN tb_MovFluidos.nivelTk
                    WHEN '5' THEN tb_MovFluidos.nivelTk
                    END
                ORDER BY tb_MovFluidos.id_Tk, day(fecha)";

                function displayTable($sql){
                    include('connection/connBD.php');
                    $mysqli = new mysqli($DBHOST ,$DBUSER, $DBPASS, $DATABASE);
                    if ($mysqli -> connect_errno) {	die( "Fallo la conexión a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error); exit;}
                    //if(!$result = mysqli_query($conexion, $sql)) die();
                    $result=$mysqli->query($sql);
                    $rawdata = array();//guardamos en un array multidimensional todos los datos de la consulta
                    $i=0;
                        
                    while($row = $result->fetch_array()){
                        $rawdata[$i] = $row;//almacena las filas de la consulta
                        $i++;
                    }
                    $close = mysqli_close($mysqli);
                        
                    //DIBUJAMOS LA TABLA
                    echo '<table width="80%" class="stack" border="1" style="text-align:center;">';
                    $columnas = count($rawdata[0])/2;
                    //echo $columnas;
                    $filas = count($rawdata);
                    //echo "<br>".$filas."<br>";
                        
                    //Añadimos los titulos
                    for($i=1;$i<count($rawdata[0]);$i=$i+2){
                        next($rawdata[0]);
                        echo "<th><b>".key($rawdata[0])."</b></th>";
                        next($rawdata[0]);
                    }
                    
                    for($i=0;$i<$filas;$i++){
                        echo "<tr>";
                        for($j=0;$j<$columnas;$j++){
                            echo "<td>".$rawdata[$i][$j]."</td>";
                        }
                        echo "</tr>";
                    }
                    echo '</table>';
                }
                //echo "<div class='row'>".$sql."</div>";
                echo displayTable($sql);
            ?>
            </div>
            
    <?php mysqli_close($mysqli);?>

        <script src="assets/js/vendor/jquery-3.1.1.min.js"></script>
        <script src="assets/js/vendor/what-input.js"></script>
        <script src="assets/js/vendor/foundation.min.js"></script>
        <script> $(document).foundation(); </script>
        <script src="assets/js/app.js"></script>
        </body>
    </html>
*/?>
    </body>
</html>