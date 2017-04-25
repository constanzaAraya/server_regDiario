<?php include('../../connection/connBD.php');
    $id=$_POST['tipo'];
    $consulta=$mysqli->query("$_POST[motor]");
    $cant=$consulta->num_rows;
    $año_mes=$año.'-'.$mes;

    header("Content-Type: application/vnd.ms-excel");
    header("Expires: 0");
    header("Pragma: no-cache");
    header('Content-type: application/x-msexcel; charset=utf-8');
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");    
    header("content-disposition: attachment;filename=HFO_Motores_".$año_mes.".xls");
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
            <thead>
                <tr align='center'>
                    <th></th>
                    <?php while($datos=$consulta->fetch_assoc()){?>
                    <td colspan='3'><strong><?php echo $datos['nombre']; $idComp[]=$datos['idComponente'];?></strong></td><?php }?>
                    <th rowspan="2">Consumo Total Diario</th>
                </tr>
                <tr align='center'>
                    <th><?php echo $mes.'_'.$año;?></strong></td>
                    <?php for($b=0;$b<$cant;$b++){?>
                        <th>Lect. Inicial</th>
                        <th>Lect. Final</th>
                        <th>Consumo Diario (Lts)</th>
                    <?php }?>
                </tr>
            <thead>
            <tbody>
                <?php for($a=1;$a<=$ultimoDia;$a++){?>
                    <tr align='center'>
                        <?php for($b=0;$b<$cant;$b++){ 
                            $idComponente=$idComp[$b];
                            $valores=$mysqli->query("SELECT idMovFluido, lecturaIni, lecturaFin, round((lecturaFin-lecturaIni),0) AS ConsumoDiario, comentarios FROM tb_MovFluidos WHERE id_Componente='$idComponente' AND fecha='$año_mes-$a'");
                            $total=$valores->num_rows;
                            $row=$valores->fetch_assoc();
                            
                            $consumo=$mysqli->query("SELECT round(sum(lecturaFin-lecturaIni),0) AS ConsumoDiario FROM tb_MovFluidos WHERE id_Componente IN (SELECT idComponente FROM tbl_Componentes JOIN tbl_TipoComponentes ON tbl_Componentes.id_TipoComponente=tbl_TipoComponentes.idTipoComponente WHERE tbl_TipoComponentes.nombre='$id') AND fecha='$año_mes-$a'");
                            $r=$consumo->fetch_assoc(); 

                            if($total>0){
                            if($b==0){?><td><?php echo $a; ?></td><?php }?>
                            <td><?php echo str_replace("-","",number_format($row['lecturaIni'],0,',','.'));?></td>
                            <td><?php if(($a<=$ayer) AND ( $total > 0)){echo str_replace("-","",number_format($row['lecturaFin'],0,',','.')); }?></td>
                            <td><?php $val=str_replace("-","",$row['ConsumoDiario']); 
                            if($total==1){echo number_format($val,0,',','.'); }?></td>
                            <?php } }?>
                            <td><?php if($total>0){ echo str_replace("-","",number_format($r['ConsumoDiario'],0,',','.'));}?></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    <?php mysqli_close($mysqli);?>
    </body>
</html>