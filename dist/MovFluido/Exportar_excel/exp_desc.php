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
    header("content-disposition: attachment;filename=Descarga_Combustible_".$año_mes.".xls");
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
                        <th colspan='7'><?php echo $name[]=$datos['nombre']; $idTK[]=$datos['idTk'];?></th>
                    <?php }?>
                </tr>
                <tr align='center'>
                    <th><?php echo $mes.'_'.$año;?></th>
                    <?php for($x=0;$x<$cant;$x++){?>
                    <th>Lectura_Inicial</th>
                    <th>Lectura_Final</th>
                    <th>Lts_Recibidos</th>
                    <?php if($name[$x] == "Diesel"){?>
                    <th>Lts_Camion</th>
                    <th colspan="2">Diferencia</th>
                    <?php }else{
                    if($name[$x] == "HFO"){?>
                    <th>Kgs_Camion</th>
                    <th>Diferencia</th>
                    <th>Lts_por_Densidad</th>
                    <?php }}?>
                    <th>Número_Guia</th>
                    <?php }?>
                </tr>
                <?php for($a=1;$a<=$ultimoDia;$a++){?>
                <tr align='center'>
                    <?php for($b=0;$b<$cant;$b++){?>
                        <?php if($b==0){?><td><?php echo $a; $mov=$_POST['movimiento'];?></td><?php }?>
                        <?php if($name[$b]=='Diesel'){
                         $valores=$mysqli->query("SELECT idMovFluido, lecturaIni, lecturaFin, comentarios, (lecturaFin-lecturaIni) AS ConsumoDiario, kg_lt_camion, kg_lt_camion-(lecturaFin-lecturaIni) AS Diferencia, nGuia FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk JOIN tbl_Fluidos ON tbl_Tk.id_Fluido=tbl_Fluidos.idFluido WHERE tbl_Tk.idTk='$idTK[$b]' AND kg_lt_camion <> '' AND fecha='$año-$mes-$a'");?>
                         <td colspan="7">
                            <table width="100%">
                            <?php while($row_val=$valores->fetch_assoc()){?>
                            <tr align="center">
                                <td><?php echo number_format($row_val['lecturaIni'],0,',','.'); $row_val['lecturaIni'];?></td>
                                <td><?php echo number_format($row_val['lecturaFin'],0,',','.');?></td>
                                <td><?php echo number_format($row_val['ConsumoDiario'],0,',','.');?></td>
                                <td><?php echo number_format($row_val['kg_lt_camion'],0,',','.');?></td>
                                <td colspan="2"><?php echo number_format($row_val['Diferencia'],0,',','.');?></td>
                                <td><?php echo $row_val['nGuia'];?></td>
                            </tr> 
                            <?php }?>
                            </table>    
                        </td>    
                        <?php }
                            if($name[$b]=='HFO'){?>
                            <?php $valores2=$mysqli->query("SELECT idMovFluido, lecturaIni, lecturaFin, comentarios, 
                                (round(lecturaFin-lecturaIni,2))*1000 AS lt_Recibido, kg_lt_camion,  
                                (kg_lt_camion-((round(lecturaFin-lecturaIni,2))*1000)) AS Diferencia, 
                                round(kg_lt_camion/(select densidad from tbl_Fluidos where idFluido='2'),0) AS lt_densidad, 
                                nGuia 
                                FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk 
                                WHERE tbl_Tk.idTk='$idTK[$b]' AND lecturaIni > 0 AND fecha='$año-$mes-$a'");
                            ?>
                            <td colspan="7">
                                <table width="100%">
                            <?php while($row_val2=$valores2->fetch_assoc()){?>
                            <tr align="center">
                                <td><?php echo number_format($row_val2['lecturaIni'],2,',','.');?></td>
                                <td><?php echo number_format($row_val2['lecturaFin'],2,',','.');?></td>
                                <td><?php echo number_format($row_val2['lt_Recibido'],0,',','.');?></td>
                                <td><?php echo number_format($row_val2['kg_lt_camion'],0,',','.');?></td>
                                <td><?php echo number_format($row_val2['Diferencia'],0,',','.');?></td>
                                <td><?php echo number_format($row_val2['lt_densidad'],0,',','.');?></td> 
                                <td><?php echo $row_val2['nGuia'];?></td>
                            </tr>
                            <?php }?>
                                </table>
                            </td>
                        <?php }?>
                    <?php }?>
                </tr>
                <?php }?>
            </table>
        <?php mysqli_close($mysqli);?>
    </body>
</html>