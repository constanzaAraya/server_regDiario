<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');
    if($tiempo_transcurrido >= 2700 or $acceso==''){  
        session_start(); 
        echo "<script>alert('Su sesion a expirado. Vuelva a iniciar sesion.')</script>";
        echo "<script>window.parent.location.href='../../session/closeLog.php';</script>";
        session_destroy(); 
        exit(); 
    }
    
    $id=base64_decode($_GET['c']);
    $dia=$_GET['d'];
    //$mov=$_GET['m'];
    $año_mes=$año.'-'.$mes;

    $motores_cons="SELECT idComponente, tbl_Componentes.nombre AS nombre FROM tbl_Componentes JOIN tbl_TipoComponentes ON tbl_Componentes.id_TipoComponente=tbl_TipoComponentes.idTipoComponente WHERE tbl_TipoComponentes.nombre='".$id."'";
    $motores=$mysqli->query($motores_cons);    
    $cant=$motores->num_rows;
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
        <div class="small-12 columns table-scroll">
                <table border="1">
                        <tr align='center'>
                            <th>
                            <?php //if($acceso=='1'){?>
                            <form method="post" action="Exportar_excel/exp_hfo.php">
                                <input type="hidden" name="tipo" value="<?php echo $id;?>">
                                <input type="hidden" name="motor" value="<?php echo $motores_cons;?>">
                                <button type="submit" name="submit" value=""/><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            </form>
                            <?php //}?>
                            </th>
                            <?php while($datos=$motores->fetch_assoc()){?>
                            <th colspan='4'><?php echo $datos['nombre']; $idComp[]=$datos['idComponente'];?></th><?php }?>
                            <th rowspan="2" bgcolor="#fff">Consumo Total Diario</th>
                        </tr>
                        <form method='post'>
                        <tr align='center'>
                            <th align="left"><?php echo $mes.'_'.$año;?></th>
                            <?php for($b=0;$b<$cant;$b++){?>
                            <th>Lectura Inicial</th>
                            <th>Lectura Final</th>
                            <th>Consumo Diario<span class="omitir">_</span>(Lts)</th>
                            <th></th>
                            <?php }?>
                        </tr>
                        <?php for($a=1;$a<=$ultimoDia;$a++){?>
                        <tr align='center'>
                            <?php for($b=0;$b<$cant;$b++){ $idComponente=$idComp[$b];
                            if($b==0){?><td><?php echo $a; ?></td><?php }?>
                            <?php $valores=$mysqli->query("SELECT idMovFluido, lecturaIni, lecturaFin, round((lecturaFin-lecturaIni),0) AS ConsumoDiario, comentarios FROM tb_MovFluidos WHERE id_Componente='$idComponente' AND fecha='$año_mes-$a'");
                                $total=$valores->num_rows;
                                $row=$valores->fetch_assoc();
                            ?>
                            <td><?php if(($a<=$ayer) AND ( $total > 0)){echo str_replace("-","",number_format($row['lecturaIni'],0,',','.')); }?></td>
                            <td><?php if(($a<=$ayer) AND ( $total > 0)){echo str_replace("-","",number_format($row['lecturaFin'],0,',','.')); }?></td>
                            <td><?php $val=str_replace("-","",$row['ConsumoDiario']); 
                            if($total==1){echo number_format($val,0,',','.'); }?></td>
                            <td>
                                <div class="row">
                                    <div class="small-2 columns">
                                    <?php if($row['comentarios']!=''){?><span data-tooltip aria-haspopup="true" class="has-tip" data-disable-hover="false" tabindex="1" title="<?php echo $row['comentarios'];?>"><i class="fa fa-cloud" aria-hidden="true"></i></span><?php }?>
                                    </div>
                                    <div class="small-2 columns">
                                        <?php if($acceso!='4'){//4:solo visualiza
                                        if(($a<=$ayer) AND ( $total > 0)){ ?>
                                        <a align='right'><button title='Editar Datos!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" idMov='<?php echo $row['idMovFluido'];?>' mov='edita' value="Editar" />
                                        <i class="fa fa-pencil" aria-hidden="true"></i></button></a>
                                        <?php }else{if($a<=$ayer){?>
                                        <button class="button-radius" title='Ingresar Consumo Motores!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" idCo="<?php echo $idComponente;?>" mov='ingresa' value="Ingresar"/><i class="fa fa-plus-square-o" aria-hidden="true"></i></button>
                                        <?php }}}?>
                                    </div>
                                </div>
                            </td>
                            <?php }?>
                            <td><?php $consumo=$mysqli->query("SELECT round(sum(lecturaFin-lecturaIni),0) AS ConsumoDiario FROM tb_MovFluidos WHERE id_Componente IN (SELECT idComponente FROM tbl_Componentes JOIN tbl_TipoComponentes ON tbl_Componentes.id_TipoComponente=tbl_TipoComponentes.idTipoComponente WHERE tbl_TipoComponentes.nombre='$id') AND fecha='$año_mes-$a'");$r=$consumo->fetch_assoc(); echo '<strong>'.str_replace("-","",number_format($r['ConsumoDiario'],0,',','.')).'</strong>';?></td>
                        </tr>
                    <?php }?>
                    </form>
                </table>
        </div>

        <div class="small reveal pop" id="modalHFO" data-reveal>
            <iframe frameBorder="0" src="" id="if2" width="100%" height="350px"></iframe>
            <button class="close-button" data-close aria-label="Close modal" type="button"><i class="fa fa-window-close" aria-hidden="true"></i></button>
        </div>

        <script type="text/javascript">
            function ingresar_datos(obj) {
                var idMov=obj.getAttribute("idMov");
                var dia=obj.getAttribute("day");
                var mov=obj.getAttribute("mov");

                if(idMov){
                    $('#modalHFO').removeClass('hide');
                    $('#if2').attr('src', "hfo.php?d="+dia+"&id="+idMov+"&m="+mov+"");
                    var popup = new Foundation.Reveal($('#modalHFO'));
                    popup.open();
                }else{       
                    var idComp=obj.getAttribute("idCo");
                    $('#modalHFO').removeClass('hide');
                    $('#if2').attr('src', "hfo.php?d="+dia+"&i="+idComp+"&m="+mov+"");
                    var popup = new Foundation.Reveal($('#modalHFO'));
                    popup.open();
                }
                return false;
            }
            function _ocultarIframe(){
                    $('#modalHFO').foundation('destroy');
                    window.location='ingresoHFO.php?c=<?php echo base64_encode('motor');?>';
                    setTimeout('redireccionar()', 0);
            }
        </script>
        <?php mysqli_close($mysqli); include('../base/foot.php');?>
    </body>
</html>