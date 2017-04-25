<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');
    if($tiempo_transcurrido >= 2700 or $acceso==''){  
        session_start(); 
        echo "<script>alert('Su sesion a expirado. Vuelva a iniciar sesion.')</script>";
        echo "<script>window.parent.location.href='../../session/closeLog.php';</script>";
        session_destroy(); 
        exit(); 
    }
    
    $TK=base64_decode($_GET['tk']);
    //$idFl=$_GET['i'];
    $tipo_cons="SELECT idTk, nombre, capacidad FROM tbl_Tk WHERE ingreso='".$TK."'";
    $tipos=$mysqli->query($tipo_cons);
    $cant=$tipos->num_rows;
    $año_mes=$año.'-'.$mes;
    //<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
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
        <div class="table-scroll">
            <div class="small-12 columns">
                <table border="1">
                    <tr align='center'>
                        <th>
                        <?php //if($acceso=='1'){?>
                        <form method="post" action="Exportar_excel/exp_tk.php">
                            <input type="hidden" name="tipos" value="<?php echo $tipo_cons;?>">
                            <button type="submit" name="submit" value=""/><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </form>
                        <?php //}?>
                        </th>
                        <?php while($datos=$tipos->fetch_assoc()){?>
                        <td colspan='4'><strong><?php echo $name[]=$datos['nombre']; $idTK[]=$datos['idTk']; $capacidad[]=$datos['capacidad'];?></strong></td><?php }?>
                    </tr>
                    <form method='post'> 
                    <tr align='center'>
                        <th><?php echo $mes.'_'.$año;?></th>
                        <?php for($x=0;$x<$cant;$x++){?>
                            <th>Nivel<span class="omitir">_</span>(%)</th>
                            <th>Capacidad<span class="omitir">_</span>(m3)</th>
                            <th>Nivel<span class="omitir">_</span>(Lts.)</th>
                            <th></th>
                        <?php }?>
                    </tr>
                    
                    <?php
                     for($a=1;$a<=$ultimoDia;$a++){?>
                        <tr align='center'>
                        <?php for($b=0;$b<$cant;$b++){
                            if($b==0){?>
                            <td><?php echo $a; /*$mov=$_POST['movimiento'];*/?></td><?php }?>
                            <?php $valores=$mysqli->query("SELECT tb_MovFluidos.idMovFluido AS idMov, round((tb_MovFluidos.nivelTk*100),2) AS Nivel, round((tb_MovFluidos.nivelTk*1000)*capacidad) AS NivelLts, comentarios FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk='".$idTK[$b]."' AND fecha='$año_mes-$a'"); 
                                $row_val=$valores->fetch_assoc();
                                $tot=$valores->num_rows;
                            ?>
                            <td>
                            <?php if($tot > 0 AND $a<=$ayer){ echo $row_val['Nivel'].'%'; }?>
                            </td>
                            <td><?php echo number_format($capacidad[$b],0,',','.');?></td>
                            <td><?php if($tot > 0){echo number_format($row_val['NivelLts'],0,',','.');}?></td>
                            <td>
                                <div class="row">      
                                    <div class="small-2 columns">
                                        <?php if($row_val['comentarios']!=''){?><span data-tooltip aria-haspopup="true" class="has-tip" data-disable-hover="false" tabindex="1" title="<?php echo $row_val['comentarios'];?>"><i class="fa fa-cloud" aria-hidden="true"></i></span><?php }?>
                                    </div>
                                    <div class="small-2 columns"><?php if($acceso!='4'){//4:solo visualiza
                                    if($tot > 0 AND $a<=$ayer){ ?>
                                        <a align='right'><button title='Editar Datos!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" id="<?php echo $row_val['idMov'];?>" movim="edita" value="Editar" />
                                        <i class="fa fa-pencil" aria-hidden="true"></i></button></a>
                                        <?php }else{if($a<=$ayer){?>
                                        <button class="button-radius" title='Ingresar Nivel TK!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" tk="<?php echo $idTK[$b];?>" movim="ingresa" value="Ingresar"/><i class="fa fa-plus-square-o" aria-hidden="true"></i></button>
                                        <?php }}}?>
                                    </div>
                                </div>
                            </td>
                        <?php }?>
                        </tr>
                    <?php }?>
                    </form>
                </table>
            </div>
        </div>

        <div class="small reveal pop" id="modalTk" data-reveal>
            <iframe frameBorder="0" src="" id="if2" width="100%" height="350px"></iframe>
            <button class="close-button" data-close aria-label="Close modal" type="button"><i class="fa fa-window-close" aria-hidden="true"></i></button>
        </div>

        <script type="text/javascript">
            function ingresar_datos(obj) {
                var dia=obj.getAttribute("day");
                var id=obj.getAttribute("id");
                var tk=obj.getAttribute("tk");
                var mov=obj.getAttribute("movim");
                if (id){
                    $('#modalTk').removeClass('hide');
                    $('#if2').attr('src', "tk.php?d="+dia+"&i="+id+"&m="+mov+"");
                    var popup = new Foundation.Reveal($('#modalTk'));
                    popup.open();
                }else{
                    $('#modalTk').removeClass('hide');
                    $('#if2').attr('src', "tk.php?d="+dia+"&t="+tk+"&m="+mov+"");
                    var popup = new Foundation.Reveal($('#modalTk'));
                    popup.open();
                } /*
                else {
                    alert("Debe seleccionar al menos un parámetro a graficar!");
                    $('#modalTk').addClass('hide');
                }*/
                return false;
            }
            /*<a href="#" id="<echo $a;?>" onclick="doalert(this); return false;">Link</a>
            function doalert(obj) {
                alert(obj.getAttribute("id"));
                return false;
            }*/

            function _ocultarIframe(){
                $('#modalTk').foundation('destroy');
                window.location='ingresoTK.php?tk=<?php echo base64_encode(1);?>';
                setTimeout('redireccionar()', 0);
            }
        </script>
        <?php mysqli_close($mysqli); include('../base/foot.php');?>
    </body>
</html>