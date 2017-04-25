<?php ini_set('session.use_only_cookies', true);
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
        <style>body{font-size: large}
            #tit{color:#fff;}
            #backgr1{background-color:#21857C;}
            #backgr2{background-color:#fff;}
        </style>
    </head>
    <body>
        <?php $fechas=$mysqli->query("SELECT DISTINCT DATE_FORMAT(fecha,'%m') as month, year(fecha) as year FROM tb_MovFluidos ORDER BY fecha ASC");
        if($_POST['fecha_datos']!=''){ $año_mes=$_POST['fecha_datos']; }
        else{ $año_mes=$años.'-'.$mess; }?>
        <div class="row collapse"><div class="small-12 columns">
            <fieldset class="fieldset">
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
            </fieldset>
        </div></div>
<?php echo '<div align="center"><strong>Período: '.$mess.'-'.$años.'</strong></div><br>';


$fluidos_=$mysqli->query("SELECT tbl_Tk.idTk as idF, tbl_Fluidos.nombre as nameF, tbl_TipoFluidos.nombre as tipoF  FROM tbl_Tk JOIN tbl_Fluidos ON tbl_Tk.id_Fluido=tbl_Fluidos.idFluido JOIN tbl_TipoFluidos ON tbl_Fluidos.id_TipoFluido=tbl_TipoFluidos.idTipoFluido WHERE ingreso='0'");
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
/*

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
 )AS datos";*/

/*  #backgr2{background-color:#f3f3e3;}
(SELECT sum(litros) FROM tb_MovFluidos WHERE id_Tk='9' AND litros <> '' AND month(fecha)='$mess' AND year(fecha)='$años') AS Total_descargado_Desminer,
        (SELECT sum(litros) FROM tb_MovFluidos WHERE id_Tk='11' AND litros <> '' AND month(fecha)='$mess' AND year(fecha)='$años') AS Total_descargado_AceiteM430
*/

              /*function displayTable($sql){
                include('../dist/connection/connBD.php');
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
                echo '<div class="row collapse"><div class="small-12 columns">
                    <table style="text-align:center;">';
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
                echo '</table></div></div>';
            }
            //echo "<div class='row'>".$sql."</div>";
            echo displayTable($sql);      3371BC*/ 
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

    <?php /*
        for($x=1;$x<31;$x++){
            $sumas_consumos=$mysqli->query("SELECT round(consumoAnterior-(nivelConsumo*capacidad)) AS consumo FROM (
            SELECT (tbl_Tk.capacidad*1000) as capacidad,
            (SELECT nivelTk FROM tb_MovFluidos WHERE id_Tk='9' AND fecha='$años-$mess-$x' AND nivelTk <= 0) AS nivelConsumo,
            (SELECT (nivelTk*(tbl_Tk.capacidad*1000)) FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='9' AND fecha=(select DATE_SUB('$años-$mess-$x', INTERVAL 1 DAY)) LIMIT 1) AS consumoAnterior
            FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk 
            WHERE id_Tk='9' AND fecha='$años-$mess-$x' LIMIT 1
            )AS datos");
            $row_sumas=$sumas_consumos->fetch_assoc();
            //if($row_sumas['consumo']<0){
            $dato_sum=$dato_sum-$row_sumas['consumo'];
            //}
        }

        for($zx=1;$zx<31;$zx++){
            $sumas_consumos2=$mysqli->query("SELECT round(consumoAnterior-(nivelConsumo*capacidad)) AS consumo2 FROM (
            SELECT (tbl_Tk.capacidad*1000) as capacidad,
            (SELECT nivelTk FROM tb_MovFluidos WHERE id_Tk='10' AND fecha='$años-$mess-$zx' AND nivelTk <= 0) AS nivelConsumo,
            (SELECT (nivelTk*(tbl_Tk.capacidad*1000)) FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='10' AND fecha=(select DATE_SUB('$años-$mess-$zx', INTERVAL 1 DAY)) LIMIT 1) AS consumoAnterior
            FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk 
            WHERE id_Tk='10' AND fecha='$años-$mess-$zx' LIMIT 1
            )AS datos");
            $row_sumas2=$sumas_consumos2->fetch_assoc();
            //if($row_sumas2['consumo']<0){
            $dato_sum2=$dato_sum2-$row_sumas2['consumo2'];
            //}
        }

        $aguas=$mysqli->query("SELECT litroDes, litroTr, saldoActualDes, saldoActualTr FROM (
        SELECT (SELECT litros FROM tb_MovFluidos WHERE id_Tk='9' AND month(fecha)='$mess' AND year(fecha)='$años' AND litros > 0 group BY id_Tk) AS litroDes,
        (SELECT litros FROM tb_MovFluidos WHERE id_Tk='10' AND month(fecha)='$mess' AND year(fecha)='$años' AND litros > 0 group BY id_Tk) AS litroTr,
        (SELECT round(nivelTk*(tbl_Tk.capacidad*1000)) FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='9' AND month(fecha)='$mess' AND year(fecha)='$años' AND nivelTk <= 0 order by fecha DESC limit 1) AS saldoActualDes,
        (SELECT round(nivelTk*(tbl_Tk.capacidad*1000)) FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='10' AND month(fecha)='$mess' AND year(fecha)='$años' AND nivelTk <= 0 order by fecha DESC limit 1) AS saldoActualTr
    ) AS datos");
        $row_aguas=$aguas->fetch_assoc();*/
    ?>
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

        <?php /*for($x=1;$x<=31;$x++){
            $sumas_consumos3=$mysqli->query("SELECT round((nivelConsumo*capacidad)-consumoAnterior) AS consumo FROM (
            SELECT (tbl_Tk.capacidad*1000) as capacidad,
            (SELECT nivelTk FROM tb_MovFluidos WHERE id_Tk='11' AND fecha='$años-$mess-$x' AND nivelTk <= 0) AS nivelConsumo,
            (SELECT (nivelTk*(tbl_Tk.capacidad*1000)) FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='11' AND fecha=(select DATE_SUB('$años-$mess-$x', INTERVAL 1 DAY)) LIMIT 1) AS consumoAnterior
            FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk 
            WHERE id_Tk='11' AND fecha='$años-$mess-$x' LIMIT 1
            )AS datos");
            $row_sumas3=$sumas_consumos3->fetch_assoc();
            $dato_sum3=$dato_sum3-$row_sumas3['consumo'];
            }
            $aceites=$mysqli->query("SELECT litro, saldoActual FROM (
            SELECT (SELECT litros FROM tb_MovFluidos WHERE id_Tk='11' AND month(fecha)='$mess' AND year(fecha)='$años' AND litros > 0 group BY id_Tk) AS litro,
            (SELECT round(nivelTk*(tbl_Tk.capacidad*1000)) FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='11' AND month(fecha)='$mess' AND year(fecha)='$años' AND nivelTk <= 0 order by fecha DESC limit 1) AS saldoActual
            ) AS datos");
            $row_aceites=$aceites->fetch_assoc();*/
        ?>
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

        <?php $horas=$mysqli->query("SELECT sum(hr_fin-hr_ini) AS Hrs FROM tbl_HrsOperacion WHERE month(fecha)='01' AND year(fecha)='2017' AND id_Componente IN (1,2,3,4,5,6) group BY id_Componente");
        while($row_horas=$horas->fetch_assoc()){$rows[]=$row_horas['Hrs'];}
        $x=0;
        ?>
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
    </body>
</html>