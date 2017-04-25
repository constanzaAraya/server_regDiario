<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');    
    if($tiempo_transcurrido >= 2700 or $acceso==''){  
        session_start(); 
        echo "<script>alert('Su sesion a expirado. Vuelva a iniciar sesion.')</script>";
        echo "<script>window.parent.location.href='../../session/closeLog.php';</script>";
        session_destroy(); 
        exit(); 
    }
    //obtencion de tipo de fluidos
    $idFl=base64_decode($_GET['d']);
    $tipos_cons="SELECT tbl_Tk.idTk, tbl_Fluidos.nombre FROM tbl_Fluidos JOIN tbl_Tk ON tbl_Fluidos.idFluido=tbl_Tk.id_Fluido WHERE id_TipoFluido='$idFl' AND ingreso='0'";
    $tipos=$mysqli->query($tipos_cons);
    $cant=$tipos->num_rows;
    //expanded row
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <?php include('../base/head.php');?>
    </head>
    <body>
        <div class='small-12 columns'>
            <table border="1">
                <tr align='center'>
                    <th>
                    <?php if($acceso=='1'){?>
                        <form method="post" action="Exportar_excel/exp_desc.php">
                            <input type="hidden" name="tipos" value="<?php echo $tipos_cons;?>">
                            <input type="hidden" name="idFluido" value="<?php echo $idFl;?>">
                            <button type="submit" name="submit" value=""/><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </form>
                    <?php }?>
                    </th>
                    <?php while($datos=$tipos->fetch_assoc()){?>
                        <th colspan='9'><?php echo $name[]=$datos['nombre']; $idTK[]=$datos['idTk'];?></th>
                    <?php }?>
                </tr>
                <form method='post'>
                <tr align='center'>
                    <th><?php echo $mes.'_'.$año;?></th>
                    <?php for($x=0;$x<$cant;$x++){?>
                    <th>Lectura_Inicial</th>
                    <th>Lectura_Final</th>
                    <th>Lts_Recibidos</th>
                    <?php if($name[$x] == "Diesel"){?>
                    <th>Lts_Camion</th>
                    <th>Diferencia</th>
                    <th></th>
                    <?php }else{
                        if($name[$x] == "HFO"){?>
                    <th>Kgs_Camion</th>
                    <th>Diferencia</th>
                    <th>Lts_por_Densidad</th>
                    <?php }}?>
                    <th>Número_Guia</th>
                    <th></th>
                    <th></th>
                    <?php }?>
                </tr>
                <?php for($a=1;$a<=$ultimoDia;$a++){?>
                <tr align='center'>
                    <?php for($b=0;$b<$cant;$b++){?>
                        <?php if($b==0){?>
                            <td><?php echo $a; $mov=$_POST['movimiento'];?></td><?php }?>
                            <?php if($name[$b]=='Diesel'){?>
                            <td colspan='8'>
                            <?php $valores=$mysqli->query("SELECT idMovFluido, lecturaIni, lecturaFin, comentarios, (lecturaFin-lecturaIni) AS ConsumoDiario, kg_lt_camion, kg_lt_camion-(lecturaFin-lecturaIni) AS Diferencia, nGuia FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk JOIN tbl_Fluidos ON tbl_Tk.id_Fluido=tbl_Fluidos.idFluido WHERE tbl_Tk.idTk='$idTK[$b]' AND kg_lt_camion <> '' AND fecha='$año-$mes-$a'");?>
                                <div class="row">
                                <?php while($row_val=$valores->fetch_assoc()){?>
                                <div class="small-2 columns"><?php echo number_format($row_val['lecturaIni'],0,',','.'); $row_val['lecturaIni'];?></div>
                                <div class="small-2 columns"><?php echo number_format($row_val['lecturaFin'],0,',','.');?></div>
                                <div class="small-2 columns"><?php echo number_format($row_val['ConsumoDiario'],0,',','.');?></div>
                                <div class="small-2 columns"><?php echo number_format($row_val['kg_lt_camion'],0,',','.');?></div>
                                <div class="small-2 columns"><?php echo number_format($row_val['Diferencia'],0,',','.');?></div>
                                <div class="small-1 columns"><?php echo $row_val['nGuia'];?></div>
                                <div class="small-1 columns"><?php if($a<=$ayer){ ?><a align='right'><button title='Editar Datos!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" idMov="<?php echo $row_val['idMovFluido'];?>" mov="Diesel" value="Editar" /><i class="fa fa-pencil" aria-hidden="true"></i></button></a><?php }?></div>
                                
                                <?php }?></div>
                            </td>        
                            <?php /*
                            $valores=$mysqli->query("SELECT idMovFluido, lecturaIni, lecturaFin, comentarios, (lecturaFin-lecturaIni) AS ConsumoDiario, kg_lt_camion, kg_lt_camion-(lecturaFin-lecturaIni) AS Diferencia, nGuia FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk JOIN tbl_Fluidos ON tbl_Tk.id_Fluido=tbl_Fluidos.idFluido WHERE tbl_Tk.idTk='$idTK[$b]' AND kg_lt_camion <> '' AND fecha='$año-$mes-$a'");?>
                            <?php while($row_val=$valores->fetch_assoc()){?>
                                <td><?php echo number_format($row_val['lecturaIni'],0,',','.'); $row_val['lecturaIni'];?></td>
                                <td><?php echo number_format($row_val['lecturaFin'],0,',','.');?></td>
                                <td><?php echo number_format($row_val['ConsumoDiario'],0,',','.');?></td>
                                <td><?php echo number_format($row_val['kg_lt_camion'],0,',','.');?></td>
                                <td><?php echo number_format($row_val['Diferencia'],0,',','.');?></td>
                                <td><?php echo $row_val['nGuia'];?></td>
                                <td align="right"><?php if($a<=$ayer){ ?><a align='right'><button title='Editar Datos!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" idMov="<?php echo $row_val['idMovFluido'];?>" mov="Diesel" value="Editar" /><i class="fa fa-pencil" aria-hidden="true"></i></button></a><?php }?></td>
                            <?php }*/?>
                            <td><?php if($a<=$ayer){?><buttom class="button-radius" title='Ingresar Datos' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" tk="<?php echo $idTK[$b];?>" fl="Diesel" value="Ingresar"/><i class="fa fa-plus-square-o" aria-hidden="true"></i></button><?php }?></td>

                            <?php }if($name[$b]=='HFO'){?>
                            <td colspan='8'>
                            <?php $valores2=$mysqli->query("SELECT idMovFluido, lecturaIni, lecturaFin, comentarios, 
                                (round(lecturaFin-lecturaIni,2))*1000 AS lt_Recibido, kg_lt_camion,  
                                (kg_lt_camion-((round(lecturaFin-lecturaIni,2))*1000)) AS Diferencia, 
                                round(kg_lt_camion/(select densidad from tbl_Fluidos where idFluido='2'),0) AS lt_densidad, 
                                nGuia 
                                FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk 
                                WHERE tbl_Tk.idTk='$idTK[$b]' AND lecturaIni > 0 AND fecha='$año-$mes-$a'");
                            ?>
                                <div class="row"><?php while($row_val2=$valores2->fetch_assoc()){?>
                                <div class="small-2 columns"><?php echo number_format($row_val2['lecturaIni'],2,',','.');?></div>
                                <div class="small-2 columns"><?php echo number_format($row_val2['lecturaFin'],2,',','.');?></div>
                                <div class="small-1 columns"><?php echo number_format($row_val2['lt_Recibido'],0,',','.');?></div>
                                <div class="small-2 columns"><?php echo number_format($row_val2['kg_lt_camion'],0,',','.');?></div>
                                <div class="small-1 columns"><?php echo number_format($row_val2['Diferencia'],0,',','.');?></div>
                                <div class="small-2 columns"><?php echo number_format($row_val2['lt_densidad'],0,',','.');?></div> 
                                <div class="small-1 columns"><?php echo $row_val2['nGuia'];?></div>
                                <div class="small-1 columns"><?php if($a<=$ayer){?><a align='right'><button title='Editar Datos!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" idMov="<?php echo $row_val2['idMovFluido'];?>" mov="HFO" value="Editar" /><i class="fa fa-pencil" aria-hidden="true"></i></button></a><?php }?></div>
                                </div>
                            <?php }?>
                            </td>
                            <td><?php if($a<=$ayer){?><button class="button-radius" title='Ingresar Datos' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" tk="<?php echo $idTK[$b];?>" fl="HFO" value="Ingresar"/><i class="fa fa-plus-square-o" aria-hidden="true"></i></button><?php }?></td>
                            <?php }?>
                    <?php }?>
                </tr>
                <?php }?>
                </form>
            </table>        
        </div>

        <div class="small reveal pop" id="modalDescarga" data-reveal>
            <iframe frameBorder="0" src="" id="if2" width="100%" height="350px"></iframe>
            <button class="close-button" data-close aria-label="Close modal" type="button"><i class="fa fa-window-close" aria-hidden="true"></i></button>
        </div>

        <script type="text/javascript">
        //envio de datos y modal con formulario
            function ingresar_datos(obj) {
                var idMov=obj.getAttribute("idMov");
                var dia=obj.getAttribute("day");
                console.log(idMov);
                if(idMov){ var mov=obj.getAttribute("mov");
                    $('#modalDescarga').removeClass('hide');
                    console.log(mov);
                    $('#if2').attr('src', "des.php?i="+idMov+"&d="+dia+"&m="+mov+"");
                    var popup = new Foundation.Reveal($('#modalDescarga'));
                    popup.open();
                }
                else{
                    var tk=obj.getAttribute("tk");
                    var fl=obj.getAttribute("fl");
                    $('#modalDescarga').removeClass('hide');
                    $('#if2').attr('src', "des.php?d="+dia+"&t="+tk+"&f="+fl+"");
                    var popup = new Foundation.Reveal($('#modalDescarga'));
                    popup.open();
                }
                return false;
            }//cierre modal
            function _ocultarIframe(){
                $('#modalDescarga').foundation('destroy');
                window.location='descargas.php?d=<?php echo base64_encode(1);?>';
                setTimeout('redireccionar()', 0);
            }
        </script>
        <?php mysqli_close($mysqli); include('../base/foot.php');?>
    </body>
</html>