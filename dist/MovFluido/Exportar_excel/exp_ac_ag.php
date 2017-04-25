<?php include('../../connection/connBD.php');
    $consulta=$mysqli->query("$_POST[tipos]");
    $cant=$consulta->num_rows;
    $año_mes=$año.'-'.$mes;
    $tipo_fl=$_POST['fluido'];
    if($tipo_fl=='2'){$tp='Agua';}
    if($tipo_fl=='3'){$tp='Aceite';}
    
    header("Content-Type: application/vnd.ms-excel");
    header("Expires: 0");
    header("Pragma: no-cache");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("content-disposition: attachment;filename=Movimiento_".$tp."_".$año_mes.".xls");
?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
    <style>body{font-family:Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif; }</style>
          <table>
                <tr align='center'>
                    <th></th>
                    <?php while($datos=$consulta->fetch_assoc()){?>
                    <th colspan='4'><?php echo $name[]=$datos['nombreF']; $idTK[]=$datos['idTk'];?></th><?php }?>
                </tr>
                <form method='post'>
                    <tr align='center'>
                        <th><?php echo $mes.'_'.$año;?></th>
                        <?php for($x=0;$x<$cant;$x++){?>
                            <th>Recibido (Lts)</th>
                            <th>Movimiento</th>
                            <th>Stock (Lts)</th>
                            <th>Nivel (%)</th>
                        <?php }?>
                    </tr>
                    <?php
                            for($a=1;$a<=$ultimoDia;$a++){?>
                                <tr align='center'>
                                <?php for($b=0;$b<$cant;$b++){
                                    $tk=$idTK[$b];
                                        $valores=$mysqli->query("SELECT litroRecibido, idR, (nivelConsumo*100) AS nivelConsumo, idC, (nivelConsumo*capacidad) AS total, consumoAnterior-(nivelConsumo*capacidad) AS consumo, comentarios FROM (
                                            SELECT nivelTk, (tbl_Tk.capacidad*1000) AS capacidad, comentarios,
                                            (SELECT litros FROM tb_MovFluidos WHERE id_Tk='".$tk."' AND fecha='".$año_mes.'-'.$a."' AND litros > 0) AS litroRecibido,
                                            (SELECT idMovFluido FROM tb_MovFluidos WHERE id_Tk='".$tk."' AND fecha='".$año_mes.'-'.$a."' AND litros > 0) AS idR,
                                            (SELECT nivelTk FROM tb_MovFluidos WHERE id_Tk='".$tk."' AND fecha='".$año_mes.'-'.$a."' AND nivelTk <= 0) AS nivelConsumo,
                                            (SELECT idMovFluido FROM tb_MovFluidos WHERE id_Tk='".$tk."' AND fecha='".$año_mes.'-'.$a."' AND nivelTk <= 0) AS idC,
                                            (SELECT (nivelTk*(tbl_Tk.capacidad*1000)) FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='".$tk."' AND fecha=(select DATE_SUB('".$año_mes.'-'.$a."', INTERVAL 1 DAY)) LIMIT 1) AS consumoAnterior
                                            FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk 
                                            WHERE id_Tk='".$tk."' AND fecha='".$año_mes.'-'.$a."' LIMIT 1
                                        )AS datos");
                                        $row_val=$valores->fetch_assoc();
                                        $tot=$valores->num_rows;
                                    if($tot>0){
                                    if($b==0){?><td><?php echo $a;?></td><?php }?>
                                    <td><?php if($row_val['litroRecibido']!= '' and $a<=$ayer){echo number_format($row_val['litroRecibido'],0,',','.');}?></td>
                                    <td><?php if($tot==1){echo number_format($row_val['consumo'],0,',','.');}?></td>
                                    <td><?php if($tot==1){echo str_replace("-","",number_format($row_val['total'],0,',','.'));}?></td>
                                    <td><?php if($row_val['nivelConsumo']!= ''){echo str_replace("-","",number_format($row_val['nivelConsumo'],2,',','.').'%');}?></td>
                                <?php }}?>
                                </tr>
                            <?php }?>
                </form>
        </table>
        <?php mysqli_close($mysqli);?>
    </body>
</html>