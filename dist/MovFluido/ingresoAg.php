<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');    
    if($tiempo_transcurrido >= 2700 or $acceso==''){  
        session_start(); 
        echo "<script>alert('Su sesion a expirado. Vuelva a iniciar sesion.')</script>";
        echo "<script>window.parent.location.href='../../session/closeLog.php';</script>";
        session_destroy(); 
        exit(); 
    }    

    $idFl=base64_decode($_GET['f']);
    
    $tipos_cons="SELECT idTk, tbl_Fluidos.nombre AS nombreF, tbl_TipoFluidos.nombre AS nombreTF FROM tbl_Tk JOIN tbl_Fluidos ON tbl_Tk.id_Fluido=tbl_Fluidos.idFluido JOIN tbl_TipoFluidos ON tbl_TipoFluidos.idTipoFluido= tbl_Fluidos.id_TipoFluido WHERE tbl_Fluidos.id_TipoFluido='".$idFl."'";
    $tipos=$mysqli->query($tipos_cons);
    $cant=$tipos->num_rows;
    //<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
    $año_mes=$año.'-'.$mes;
    /*bgcolor="#EEEEEE"*/
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <?php include('../base/head.php');?>
        <style>.omitir{ color: #fff; }
        </style>
    </head>
    <body>
            <div class="small-12 columns table-scroll" align="center">
                    <input type="hidden" name="fluido" value="<?php echo $idFl;?>" id='agua' />
                        <table border="1">
                            <tr align='center'>
                                <th>
                                <?php /*if($acceso=='1'){*/?>
                                <form method="post" action="Exportar_excel/exp_ac_ag.php">
                                    <input type="hidden" name="tipos" value="<?php echo $tipos_cons;?>">
                                    <input type="hidden" name="fluido" value="<?php echo $idFl;?>">
                                    <button type="submit" name="submit" value=""/><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                                </form>
                                <?php //}?>
                                </th>
                                <?php while($datos=$tipos->fetch_assoc()){?>
                                <th colspan='5'><?php echo $name[]=$datos['nombreF']; $idTK[]=$datos['idTk'];?></th><?php }?>
                            </tr>
                            <form method='post'>
                            <tr align='center'>
                                <th><?php echo $mes.'_'.$año;?></th>
                            <?php for($x=0;$x<$cant;$x++){?>
                                <th>Recibido<span class="omitir">_</span>(Lts)</th>
                                <th>Movimiento</th>
                                <th>Stock<span class="omitir">_</span>(Lts)</th>
                                <th>Nivel<span class="omitir">_</span>(%)</th>
                                <th></th>
                            <?php }?>
                            </tr>
                            <?php
                            for($a=1;$a<=$ultimoDia;$a++){?>
                                <tr align='center'>
                                <?php for($b=0;$b<$cant;$b++){
                                    if($b==0){?>
                                    <td><?php echo $a;?></td><?php }?>
                                    <?php $tk=$idTK[$b];
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
                                    ?>
                                    <td>
                                        <div class="small-1 columns"><?php if($row_val['litroRecibido']!= '' and $a<=$ayer){echo number_format($row_val['litroRecibido'],0,',','.');}?></div>
                                        <div class="small-1 columns">
                                        <?php if($acceso!='4'){//4:solo visualiza
                                        if($row_val['litroRecibido']!= ''){ ?>
                                        <a><button type="button" onclick="ingresar_datos(this); return false;" title='Editar Datos!' id='<?php echo $row_val["idR"];?>' day="<?php echo $a;?>" tipoF='<?php echo $idFl;?>' mov='recibo' value="Editar" />
                                        <i class="fa fa-pencil" aria-hidden="true"></i></button></a>
                                        <?php }if(($a<=$ayer) and ($row_val['litroRecibido']== '')){?>
                                        <button class="button-radius" title='Ingresar Descarga!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" tk="<?php echo $tk;?>" mov='recibo' value="Ingresa" /><i class="fa fa-plus-square-o" aria-hidden="true"></i></button>
                                        <?php }}?>
                                        </div>
                                    </td>
                                    <td><?php if($tot==1){echo number_format($row_val['consumo'],0,',','.');}?></td>
                                    <td><?php if($tot==1){echo str_replace("-","",number_format($row_val['total'],0,',','.'));}?></td>
                                    <td><?php if($row_val['nivelConsumo']!= ''){echo str_replace("-","",number_format($row_val['nivelConsumo'],2,',','.').'%');}?></td>
                                    <td> 
                                        <div class="row">
                                            <div class="small-1 columns">
                                                <?php if($row_val['comentarios']!=''){?><span data-tooltip aria-haspopup="true" class="has-tip" data-disable-hover="false" tabindex="1" title="<?php echo $row_val['comentarios'];?>"><i class="fa fa-cloud" aria-hidden="true"></i></span><?php }?>
                                            </div>
                                            <div class="small-1 columns">
                                                <?php if($acceso!='4'){//4:solo visualiza
                                                if(($a<=$ayer) and $row_val['nivelConsumo']!= ''){ ?>
                                                <a><button type="button" onclick="ingresar_datos(this); return false;" title='Editar Datos!' id='<?php echo $row_val["idC"];?>' day="<?php echo $a;?>" tipoF='<?php echo $idFl;?>' mov='consumo' value="Editar" />
                                                <i class="fa fa-pencil" aria-hidden="true"></i></button></a>
                                                <?php }
                                                    if(($a<=$ayer) and ($row_val['nivelConsumo']== '')){ ?>
                                                    <button class="button-radius" title='Ingresar Datos!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" tk="<?php echo $tk;?>" tipoF='<?php echo $idFl;?>' mov='consumo' value="Ingresa" /><i class="fa fa-plus-square-o" aria-hidden="true"></i></button>
                                                <?php }}?>
                                            </div>
                                        </div>
                                    </td>
                                <?php }?>
                                </tr>
                            <?php }?>
                        </form>
                    </table>
            </div>
            
        <div class="reveal pop" id="modal" data-reveal>
            <iframe frameBorder="0" src="" id="if1" width="100%" height="350px"></iframe>
            <button class="close-button" data-close aria-label="Close modal" type="button"><i class="fa fa-window-close" aria-hidden="true"></i></button> 
        </div>

        <script type="text/javascript">
            function ingresar_datos(obj) {
                var id=obj.getAttribute("id");
                var TpoFl=obj.getAttribute("tipoF");
                var dia=obj.getAttribute("day");
                var mov=obj.getAttribute("mov");
                if(id){
                    $('#modal').removeClass('hide');
                    $('#if1').attr('src', "ag.php?d="+dia+"&i="+id+"&f="+TpoFl+"&m="+mov+"");   
                    var popup = new Foundation.Reveal($('#modal'));
                    popup.open();
                }
                else{
                    var tk=obj.getAttribute("tk");
                    $('#modal').removeClass('hide');
                    $('#if1').attr('src', "ag.php?d="+dia+"&t="+tk+"&f="+TpoFl+"&m="+mov+"");
                    var popup = new Foundation.Reveal($('#modal'));
                    popup.open();
                }
                return false;
            }
            /*<a href="#" id="<echo $a;?>" onclick="doalert(this); return false;">Link</a>
            function doalert(obj) {
                alert(obj.getAttribute("id"));
                return false;
            }*/
            
            function _ocultarIframe(){
                //document.getElementById('if1').style.display = 'none';
                $('#modal').foundation('destroy');
                <?php if($idFl=='2'){?>
                window.location='ingresoAg.php?f=<?php echo base64_encode(2);?>';
                <?php }?>
                <?php if($idFl=='3'){?>
                window.location='ingresoAg.php?f=<?php echo base64_encode(3);?>';
                <?php }?>
                setTimeout('redireccionar()', 0);
            }
        </script> 
        <?php mysqli_close($mysqli); include('../base/foot.php');?>
    </body>
</html>