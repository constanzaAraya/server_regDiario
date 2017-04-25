<?php include('../../connection/connBD.php');
    $consulta=$mysqli->query("$_POST[tipos]");
    $cant=$consulta->num_rows;
    $año_mes=$año.'-'.$mes;
    $idFl=$_POST['idFluido'];
    
    header("Content-Type: application/vnd.ms-excel");
    header("Expires: 0");
    header("Pragma: no-cache");
    header('Content-type: application/x-msexcel; charset=utf-8');
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");    
    header("content-disposition: attachment;filename=Combustible_".$año_mes.".xls");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <style>body{font-family:Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif; }</style>
    </head>
    <body>
            <table>
                <tr align='center'>
                    <th></th>
                    <?php while($datos=$consulta->fetch_assoc()){?> 
                    <th colspan='7'><?php echo $name[]=$datos['nombre']; $idTK[]=$datos['idTk'];?></th><?php }?>
                </tr>
                <form method='post'>
                <tr align='center'>
                    <th><?php echo $mes.'_'.$año;?></th>
                    <?php for($x=0;$x<$cant;$x++){
                    if($name[$x] == "Diesel"){?>
                    <th>Lts. Recibido</th>
                    <th>Nivel Inicial</th>
                    <th>Nivel Final</th>
                    <th>Consumo Motores(Lts)</th>
                    <th>Consumo Caldera(Lts)</th>
                    <th>Consumo Diario(Lts)</th>
                    <th>Stock (Lts)</th>
                <?php }else{
                    if($name[$x] == "HFO"){?>
                    <th>Lts. Recibidos</th>
                    <th>Ton</th>
                    <th>Consumo Diario(Lts)</th>
                    <th>Ton</th>
                    <th>Stock Lts.</th>
                    <th>Ton</th>
                <?php }}
                }?>
                </tr>
                <?php
                 for($a=1;$a<=$ultimoDia;$a++){?>
                <tr align='center'>
                    <?php for($b=0;$b<$cant;$b++){
                        if($name[$b]=='Diesel'){
                            $valores=$mysqli->query("SELECT Recibido, id1, lecturaIni, lecturaFin, consumoM, id2, cCaldera,    consumoM-cCaldera AS ConsumoDiario, valorInicial, comentarios FROM (
                                    SELECT idMovFluido AS id1, lecturaIni, lecturaFin, lecturaFin-lecturaIni AS consumoM, comentarios,
                                        (SELECT litros FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Componente='9') AS cCaldera,
                                        (SELECT idMovFluido FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Componente='9') AS id2,
    	                                (SELECT Valor FROM tbl_ValoresIniciales WHERE id_Tk='$idTK[$b]' AND id_Componente='9') AS valorInicial,
                                        (SELECT sum(kg_lt_camion) FROM `tb_MovFluidos` WHERE id_Tk='$idTK[$b]' AND fecha='$año_mes-$a' AND lecturaIni > 0) AS Recibido
                                    FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' and lecturaIni < 0 
                                    ) AS dat LIMIT 1");
                            $row=$valores->fetch_assoc();
                            $total=$valores->num_rows;
                        }
                        if($name[$b]=='HFO'){
                            $consumoDia=$mysqli->query("SELECT Recibido, (Recibido*densidad)/1000000 AS Ton1, ConsumoDiario, Ton2, Stock FROM (
                            SELECT 
                                round(sum(lecturaFin-lecturaIni),0) ConsumoDiario, 
                                (SELECT round(sum((lecturaFin-lecturaIni)*1000)) AS Recibido FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' AND lecturaIni > 0) AS Recibido,
                                round(((sum(lecturaFin-lecturaIni))*(SELECT densidad*1000 FROM tbl_Fluidos WHERE nombre='$name[$b]'))/1000000,4) AS Ton2,
                                round((SELECT densidad FROM tbl_Fluidos WHERE nombre='$name[$b]')*1000,1) AS densidad,  
                                CASE
                                    WHEN (SELECT COUNT(*) FROM tb_MovFluidos WHERE id_Tk='$idTK[$b]' AND month(fecha)='$mes' AND year(fecha)='$año' AND lecturaIni > 0) > 1 
                                    THEN ((SELECT round(sum((lecturaFin-lecturaIni)*1000)) AS Recibido FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' AND lecturaIni > 0)-round(sum(lecturaFin-lecturaIni),0))+(SELECT Valor FROM tbl_ValoresIniciales WHERE id_Tk='$idTK[$b]')
                                    WHEN (SELECT COUNT(*) FROM tb_MovFluidos WHERE id_Tk='$idTK[$b]' AND month(fecha)='$mes' AND year(fecha)='$año' AND lecturaIni > 0) <= 1 
                                    THEN (SELECT Valor FROM tbl_ValoresIniciales WHERE id_Tk='$idTK[$b]')
                                END AS Stock    
                                FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk
                                WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' AND lecturaIni < 0
                                )as datos");
                                $row_consumo=$consumoDia->fetch_assoc();
                                $tot=$consumoDia->num_rows;
                        }
                        if($total>0){
                     if($b==0){?><td><?php echo $a;?></td><?php }?>
                    <?php if($name[$b]=='Diesel'){ ?>
                        <td><?php $recibo=$mysqli->query("SELECT sum(kg_lt_camion) AS Recibido FROM `tb_MovFluidos` WHERE id_Tk='$idTK[$b]' AND fecha='$año_mes-$a' AND lecturaIni > 0"); $recibo_=$recibo->fetch_assoc();
                            if($recibo_['Recibido']!=''){echo number_format($recibo_['Recibido'],0,',','.'); }?>
                        </td>
                        <td><?php echo str_replace("-","",number_format($row['lecturaIni'],0,',','.'));?></td>
                        <td><?php if(($a<=$ayer) AND ( $total > 0)){echo str_replace("-","",number_format($row['lecturaFin'],0,',','.'));}?></td>
                        <td><?php if($total > 0){echo str_replace("-","",number_format($row['consumoM'],0,',','.'));}?></td>
                        <td><?php $consumoCaldera=$mysqli->query("SELECT id2, cCaldera, comentarios from (
                                        SELECT comentarios, 
                                            (SELECT litros FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Componente='9') AS cCaldera,
                                            (SELECT idMovFluido FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Componente='9') AS id2 
                                        FROM tb_MovFluidos 
                                        WHERE fecha='$año_mes-$a' and litros >=0
                                    ) AS dat LIMIT 1");
                                    $row_consumoCaldera=$consumoCaldera->fetch_assoc();?>
                                    <?php if(($a<=$ayer) and ($row_consumoCaldera['cCaldera'] != '')){ echo number_format($row_consumoCaldera['cCaldera'],0,',','.'); }?>
                        </td>
                        <td><?php echo str_replace("-","",number_format($row['ConsumoDiario'],0,',','.')); ?></td>    
                        <td><?php /*echo $row['valorInicial'];*/?></td>
                    <?php } //fin diesel
                    if($name[$b]=='HFO'){ ?>
                        <td><?php $recibo2=$mysqli->query("SELECT sum(lecturaFin-lecturaIni)*1000 AS Recibido FROM `tb_MovFluidos` WHERE id_Tk='$idTK[$b]' AND fecha='$año_mes-$a' AND lecturaIni > 0");
                        $recibo_2=$recibo2->fetch_assoc();
                            if($recibo_2['Recibido']!=''){ echo number_format($recibo_2['Recibido'],0,',','.'); }?>
                        </td>
                        <td><?php if($total==1){echo number_format($row_consumo['Ton1'],4,',','.');}?></td>
                        <td><?php if($row_consumo['ConsumoDiario']!=''){echo str_replace("-","",number_format($row_consumo['ConsumoDiario'],0,',','.'));}?></td>
                        <td><?php if($total==1){echo str_replace("-","",number_format($row_consumo['Ton2'],4,',','.'));}?></td>
                        <td><?php /*if($total==1){echo number_format($row_consumo['Stock'],0,',','.');}*/?></td>
                        <td></td>
                    <?php }?>
                    <?php }}?>
                </tr>
                <?php }?>
                </form>
            </table>
        <?php mysqli_close($mysqli);?>
    </body>
</html>