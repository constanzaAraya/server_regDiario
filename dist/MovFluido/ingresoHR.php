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
    $mov=$_GET['m'];
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
    </head>
    <body>
        <div class="small-12 columns">
            <div class="table-scroll">
                    <table border="1">
                        <tr align='center'>
                            <th>
                            <?php //if($acceso=='1'){?>
                            <form method="post" action="Exportar_excel/exp_hrs.php">
                                <input type="hidden" name="motor" value="<?php echo $motores_cons;?>">
                                <button type="submit" name="submit" value=""/><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            </form>
                            <?php //}?>
                            </th>
                            <?php while($datos=$motores->fetch_assoc()){?>
                            <th colspan='4'><?php echo $datos['nombre']; $idComp[]=$datos['idComponente'];?></th><?php }?>
                            <th rowspan="2" bgcolor="#fff">Total Horas Diarias</th>
                        </tr>
                        <form method='post'>
                        <tr align='center'>
                            <th><?php echo $mes.'_'.$año;?></th>
                            <?php for($b=0;$b<$cant;$b++){?>
                            <th>Horometro Inicial</th>
                            <th>Horometro Final</th>
                            <th>Horas Diarias</th>
                            <th></strong></td>
                            <?php }?>
                        </tr>
                        <?php
                        for($a=1;$a<=$ultimoDia;$a++){?>
                            <tr align='center'>
                                <?php for($b=0;$b<$cant;$b++){
                                    if($b==0){?><td><?php echo $a; ?></td><?php }?>
                                    <?php $valores=$mysqli->query("SELECT idHoras, hr_ini, hr_fin, hr_fin-hr_ini AS Hrs, comentario FROM tbl_HrsOperacion WHERE fecha='$año_mes-$a' AND id_Componente='$idComp[$b]'");
                                        $total=$valores->num_rows;
                                        $row=$valores->fetch_assoc();
                                    ?>
                                    <td><?php echo $row['hr_ini'];?></td>
                                    <td><?php if(($a<=$ayer) AND ( $total > 0)){echo $row['hr_fin']; }?></td>
                                    <td><?php echo $row['Hrs']; ?></td>
                                    <td>
                                        <div class="row">
                                            <div class="small-1 columns">
                                                <?php if($row['comentario']!=''){?><span data-tooltip aria-haspopup="true" class="has-tip" data-disable-hover="false" tabindex="1" title="<?php echo $row['comentario'];?>"><i class="fa fa-cloud" aria-hidden="true"></i></span><?php }?>
                                            </div>
                                            <div class="small-2 columns"><?php if($acceso!='4'){//4:solo visualiza
                                            if(($a<=$ayer) AND ( $total > 0)){ ?>
                                            <a align='right'><button title='Editar Datos!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" idMov='<?php echo $row['idHoras'];?>' mov='edita' value="Editar" />
                                            <i class="fa fa-pencil" aria-hidden="true"></i></button></a>
                                            <?php }else{if($a<=$ayer){?>
                                                <button class="button-radius" title='Ingresar Consumo Motores!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" idCo="<?php echo $idComp[$b];?>" mov='ingresa' value="Ingresar"/><i class="fa fa-plus-square-o" aria-hidden="true"></i></button>
                                            <?php }}}?></div>
                                        </div>
                                    </td>
                                <?php }?>
                                <td><?php $totalHoras=$mysqli->query("SELECT sum(hr_fin-hr_ini) as horas FROM `tbl_HrsOperacion` WHERE fecha='$año_mes-$a'");
                                $r=$totalHoras->fetch_assoc(); 
                                echo '<strong>'.$r['horas'].'</strong>';?></td>
                            </tr>
                        <?php }?>
                        </form>
                    </table>
                </div>            
        </div>

        <div class="small reveal pop" id="modalHr" data-reveal>
            <iframe frameBorder="0" src="" id="if2" width="100%" height="350px"></iframe>
            <button class="close-button" data-close aria-label="Close modal" type="button"><i class="fa fa-window-close" aria-hidden="true"></i></button>
        </div>

        <script type="text/javascript">
            function ingresar_datos(obj) {
                var idMov=obj.getAttribute("idMov");
                var dia=obj.getAttribute("day");
                var mov=obj.getAttribute("mov");

                if(idMov){
                    $('#modalHr').removeClass('hide');
                    $('#if2').attr('src', "hr.php?d="+dia+"&id="+idMov+"&m="+mov+"");
                    var popup = new Foundation.Reveal($('#modalHr'));
                    popup.open();
                }else{       
                    var idComp=obj.getAttribute("idCo");
                    $('#modalHr').removeClass('hide');
                    $('#if2').attr('src', "hr.php?d="+dia+"&i="+idComp+"&m="+mov+"");
                    var popup = new Foundation.Reveal($('#modalHr'));
                    popup.open();
                }
                return false;
            }
            function _ocultarIframe(){
                    $('#modalHr').foundation('destroy');
                    window.location='ingresoHR.php?c=<?php echo base64_encode('motor');?>';
                    setTimeout('redireccionar()', 0);
            }
        </script>
        <?php mysqli_close($mysqli); include('../base/foot.php');?>
    </body>
</html>