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
        <style>.omitir{ color: #fff; }</style>
    </head>
    <body>
        <div class="row">
        <div class='small-12 columns table-scroll' align='center'>
            <table border="1">
                <tr align='center'>
                    <th>
                     <?php //if($acceso=='1'){?>
                        <form method="post" action="Exportar_excel/exp_desc.php">
                            <input type="hidden" name="tipos" value="<?php echo $tipos_cons;?>">
                            <input type="hidden" name="idFluido" value="<?php echo $idFl;?>">
                            <button type="submit" name="submit" value=""/><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </form>
                     <?php //}?>
                    </th>
                    <?php while($datos=$tipos->fetch_assoc()){?>
                        <th colspan='9'><?php echo $name[]=$datos['nombre']; $idTK[]=$datos['idTk'];?></th>
                    <?php }?>
                </tr>
                <form method='post'>
                <tr align='center'>
                    <th><?php echo $mes.'_'.$año;?></th>
                    <?php for($x=0;$x<$cant;$x++){?>
                    <th>Lectura<span class="omitir">_</span>Inicial</th>
                    <th>Lectura<span class="omitir">_</span>Final</th>
                    <th>Lts.<span class="omitir">_</span>Recibidos</th>
                    <?php if($name[$x] == "Diesel"){?>
                    <th>Lts.<span class="omitir">_</span>Camion</th>
                    <th colspan="2">Diferencia</th>
                    <?php }else{
                        if($name[$x] == "HFO"){?>
                    <th>Kgs.<span class="omitir">_</span>Camion</th>
                    <th>Diferencia</th>
                    <th>Lts.<span class="omitir">_</span>por<span class="omitir">_</span>Densidad</th>
                    <?php }}?>
                    <th colspan="2">Número<span class="omitir">_</span>Guia</th>
                    <th></th>
                    <?php }?>
                </tr>
                <?php for($a=1;$a<=$ultimoDia;$a++){?>
                <tr align='center'>
                    <?php for($b=0;$b<$cant;$b++){?>
                        <?php if($b==0){?>
                            <td><?php echo $a; $mov=$_POST['movimiento'];?></td><?php }?>
                            <?php if($name[$b]=='Diesel'){?>
                            <?php $valores=$mysqli->query("SELECT idMovFluido, lecturaIni, lecturaFin, comentarios, (lecturaFin-lecturaIni) AS ConsumoDiario, kg_lt_camion, kg_lt_camion-(lecturaFin-lecturaIni) AS Diferencia, nGuia FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk JOIN tbl_Fluidos ON tbl_Tk.id_Fluido=tbl_Fluidos.idFluido WHERE tbl_Tk.idTk='$idTK[$b]' AND kg_lt_camion <> '' AND fecha='$año-$mes-$a'");?>
                            <td colspan="8">
                                <table width="100%">
                                <?php while($row_val=$valores->fetch_assoc()){?>
                                <tr align="center">
                                    <td width="120" align="left"><?php echo number_format($row_val['lecturaIni'],0,',','.'); $row_val['lecturaIni'];?></td>
                                    <td width="130"><?php echo number_format($row_val['lecturaFin'],0,',','.');?></td>
                                    <td width="130"><?php echo number_format($row_val['ConsumoDiario'],0,',','.');?></td>
                                    <td width="130"><?php echo number_format($row_val['kg_lt_camion'],0,',','.');?></td>
                                    <td width="100" colspan="2"><?php echo number_format($row_val['Diferencia'],0,',','.');?></td>
                                    <td width="80" align="right"><?php echo $row_val['nGuia'];?></td>
                                    <td align="right"><?php if($acceso!='4'){//4:solo visualiza
                                    if($a<=$ayer){ ?><a align='right'><button title='Editar Datos!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" idMov="<?php echo $row_val['idMovFluido'];?>" mov="Diesel" value="Editar" /><i class="fa fa-pencil" aria-hidden="true"></i></button></a><?php }}?></td>
                                </tr>
                                <?php }?>
                                </table>
                            </td>    
                            <td><?php if($acceso!='4'){//4:solo visualiza
                            if($a<=$ayer){?><buttom class="button-radius" title='Ingresar Datos' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" tk="<?php echo $idTK[$b];?>" fl="Diesel" value="Ingresar"/><i class="fa fa-plus-square-o" aria-hidden="true"></i></button><?php }}?></td>
                            <?php }
                            if($name[$b]=='HFO'){ 
                             /*$valores2=$mysqli->query("SELECT idMovFluido, lecturaIni, lecturaFin, comentarios, 
                                (round(lecturaFin-lecturaIni,2))*1000 AS lt_Recibido, kg_lt_camion,  
                                (kg_lt_camion-((round(lecturaFin-lecturaIni,2))*1000)) AS Diferencia, 
                                round(kg_lt_camion/(select densidad from tbl_Fluidos where idFluido='2'),0) AS lt_densidad, 
                                nGuia 
                                FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk 
                                WHERE tbl_Tk.idTk='$idTK[$b]' AND lecturaIni > 0 AND fecha='$año-$mes-$a'");*/
                                $valores2=$mysqli->query("SELECT idMovFluido, lecturaIni, lecturaFin, comentarios, 
                                (round(lecturaFin-lecturaIni,2))*1000 AS lt_Recibido, kg_lt_camion,  
                                (kg_lt_camion-((round(lecturaFin-lecturaIni,2))*1000)) AS Diferencia, 
                                round(kg_lt_camion/(0.9679),0) AS lt_densidad, 
                                nGuia 
                                FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk 
                                WHERE tbl_Tk.idTk='$idTK[$b]' AND lecturaIni > 0 AND fecha='$año-$mes-$a'");
                            ?>
                            <td colspan="8">
                                <table width="100%">
                                <?php while($row_val2=$valores2->fetch_assoc()){?>
                                <tr align="center">
                                    <td width="125"><?php echo number_format($row_val2['lecturaIni'],2,',','.');?></td>
                                    <td width="125"><?php echo number_format($row_val2['lecturaFin'],2,',','.');?></td>
                                    <td width="130"><?php echo number_format($row_val2['lt_Recibido'],0,',','.');?></td>
                                    <td width="125"><?php echo number_format($row_val2['kg_lt_camion'],0,',','.');?></td>
                                    <td width="125"><?php echo number_format($row_val2['Diferencia'],0,',','.');?></td>
                                    <td width="130"><?php echo number_format($row_val2['lt_densidad'],0,',','.');?></td> 
                                    <td width="80" align="right"><?php echo $row_val2['nGuia'];?></td>
                                    <td align="right"><?php if($acceso!='4'){//4:solo visualiza
                                    if($a<=$ayer){?><a><button title='Editar Datos!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" idMov="<?php echo $row_val2['idMovFluido'];?>" mov="HFO" value="Editar" /><i class="fa fa-pencil" aria-hidden="true"></i></button></a><?php }}?></td>
                                </tr>
                                <?php }?>
                                </table>
                            </td>
                            <td align="rigth"><?php if($acceso!='4'){//4:solo visualiza
                            if($a<=$ayer){?><button class="button-radius" title='Ingresar Datos' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" tk="<?php echo $idTK[$b];?>" fl="HFO" value="Ingresar"/><i class="fa fa-plus-square-o" aria-hidden="true"></i></button><?php }}?></td>
                            <?php }?>
                    <?php }?>
                </tr>
                <?php }?>
                </form>
            </table>
        </div>
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