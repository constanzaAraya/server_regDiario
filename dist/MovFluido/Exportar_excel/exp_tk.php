<?php include('../../connection/connBD.php');
    $tipos=$mysqli->query("$_POST[tipos]");
    $cant=$tipos->num_rows;
    $año_mes=$año.'-'.$mes;

    header("Content-Type: application/vnd.ms-excel");
    //header("Content-type: application/x-msdownload");
    header("Expires: 0");
    header("Pragma: no-cache");
    header('Content-type: application/x-msexcel; charset=utf-8');
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");    
    header("content-disposition: attachment;filename=Nivel_Estanques_".$año_mes.".xls");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <style>body{font-family:Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif; }</style>
    </head>
    <body>
            <table align="center">
                <thead>
                    <tr>
                        <th></th>
                        <?php while($datos=$tipos->fetch_assoc()){?>
                        <th colspan="3"><?php echo $name[]=$datos['nombre']; $idTK[]=$datos['idTk']; $capacidad[]=$datos['capacidad'];?></th><?php }?>
                    </tr>
                    <tr>
                        <th><?php echo $mes.'_'.$año;?></th>
                        <?php for($x = 0;$x < $cant;$x++){?>
                            <th>Nivel (%)</th>
                            <th>Capacidad (m3)</th>
                            <th>Nivel (Lts.)</th>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                    <?php for($a=1;$a<=$ultimoDia;$a++){?>
                        <tr>
                        <?php for($b=0;$b<$cant;$b++){
                            $valores=$mysqli->query("SELECT tb_MovFluidos.idMovFluido AS idMov, round((tb_MovFluidos.nivelTk*100),2) AS Nivel, round((tb_MovFluidos.nivelTk*1000)*capacidad) AS NivelLts, comentarios FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='".$idTK[$b]."' AND fecha='$año_mes-$a'"); 
                            $row_val=$valores->fetch_assoc();
                            $tot=$valores->num_rows;
                            if($tot > 0){
                            if($b==0){?><td><?php echo $a;?></td><?php }?>
                            <td><?php if($tot > 0 AND $a<=$ayer){ echo $row_val['Nivel'].'%'; }?></td>
                            <td><?php echo number_format($capacidad[$b],0,',','.');?></td>
                            <td><?php if($tot > 0){echo number_format($row_val['NivelLts'],0,',','.');}?></td>
                        <?php }}?>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
    <?php mysqli_close($mysqli);?>
</body>
</html>