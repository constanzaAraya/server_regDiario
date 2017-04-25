<?php include('../../connection/connBD.php');
    $consulta=$mysqli->query("$_POST[motor]");
    $cant=$consulta->num_rows;
    $año_mes=$año.'-'.$mes;

    header("Content-Type: application/vnd.ms-excel");
    header("Expires: 0");
    header("Pragma: no-cache");
    header('Content-type: application/x-msexcel; charset=utf-8');
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");    
    header("content-disposition: attachment;filename=Horometro_Motores_".$año_mes.".xls");
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
                        <tr align='center'>
                            <th></th>
                            <?php while($datos=$consulta->fetch_assoc()){?>
                            <th colspan='3'><?php echo $datos['nombre']; $idComp[]=$datos['idComponente'];?></th><?php }?>
                            <th rowspan="2">Total Hrs_Diarias</th>
                        </tr>
                        <tr align='center'>
                            <th><?php echo $mes.'_'.$año;?></th>
                            <?php for($b=0;$b<$cant;$b++){?>
                            <th>Hrm_Inicial</th>
                            <th>Hrm_Final</th>
                            <th>Hrs_Diarias</th>
                            <?php }?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for($a=1;$a<=$ultimoDia;$a++){?>
                            <tr align='center'>
                                <?php for($b=0;$b<$cant;$b++){
                                    $valores=$mysqli->query("SELECT idHoras, hr_ini, hr_fin, hr_fin-hr_ini AS Hrs, comentario FROM tbl_HrsOperacion WHERE fecha='$año_mes-$a' AND id_Componente='$idComp[$b]'");
                                    $total=$valores->num_rows;
                                    $row=$valores->fetch_assoc();

                                    $totalHoras=$mysqli->query("SELECT sum(hr_fin-hr_ini) as horas FROM `tbl_HrsOperacion` WHERE fecha='$año_mes-$a'");
                                    $r=$totalHoras->fetch_assoc(); 

                                    if($total>0){
                                    if($b==0){?><td><?php echo $a; ?></td><?php }?>
                                    <td><?php echo $row['hr_ini'];?></td>
                                    <td><?php if(($a<=$ayer) AND ( $total > 0)){echo $row['hr_fin']; }?></td>
                                    <td><?php echo $row['Hrs']; ?></td>
                                <?php }}?>
                                <td><?php echo '<strong>'.$r['horas'].'</strong>';?></td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
        <?php mysqli_close($mysqli);?>
    </body>
</html>