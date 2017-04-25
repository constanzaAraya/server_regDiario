<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');
    
    $id=$_GET['i'];
    $dia=$_GET['d'];
    $tk=$_GET['t'];
    $mov=$_GET['m'];
    $fecha=$año.'-'.$mes.'-'.$dia;
    $ayer=date('Y-m-d', strtotime($fecha) - 3600);

    if(isset($mov)){
        $buscar=$mysqli->query("SELECT * FROM tb_MovFluidos WHERE idMovFluido='$id'");
        if($buscar->num_rows>0){
            $row=$buscar->fetch_assoc();
            if($row['lecturaIni'] < 0 or $row['lecturaFin'] < 0){
                $lectura=$mysqli->query("select substring_index(lecturaIni,'-',-1) as lectIni, substring_index(lecturaFin,'-',-1) as lectFin FROM tb_MovFluidos WHERE idMovFluido='$id'");
                $row_lect=$lectura->fetch_assoc();
                $lectIni=$row_lect['lectIni'];
                $lectFin=$row_lect['lectFin'];
            }else{ 
                $lectIni=$row['lecturaIni'];
                $lectFin=$row['lecturaFin'];
            }
        }
        if($mov=='caldera'){
            $datos=$mysqli->query("SELECT * FROM tb_MovFluidos WHERE idMovFluido='$id'");
            $row=$datos->fetch_assoc();
        }
    }else{
        $datosAnteriores=$mysqli->query("SELECT lecturaFin FROM tb_MovFluidos WHERE id_Tk='".$tk."' AND fecha=(select DATE_SUB('".$fecha."', INTERVAL 1 DAY)) AND lecturaIni <= 0 ");
        if($datosAnteriores->num_rows>0){
            $rows=$datosAnteriores->fetch_assoc();
            $lectIni=str_replace("-","",$rows['lecturaFin']);
        }else{
            $buscar=$mysqli->query("SELECT * FROM tb_MovFluidos WHERE idMovFluido='$id'");
            if($buscar->num_rows>0){
                $row=$buscar->fetch_assoc();
                if($row['lecturaIni'] < 0 or $row['lecturaFin'] < 0){
                    $lectura=$mysqli->query("select substring_index(lecturaIni,'-',-1) as lectIni, substring_index(lecturaFin,'-',-1) as lectFin FROM tb_MovFluidos WHERE idMovFluido='$id'");
                    $row_lect=$lectura->fetch_assoc();
                    $lectIni=$row_lect['lectIni'];
                    $lectFin=$row_lect['lectFin'];
                }else{ 
                    $lectIni=$row['lecturaIni'];
                    $lectFin=$row['lecturaFin'];
                }
            }
        }
        if($mov=='caldera'){
            $datos=$mysqli->query("SELECT * FROM tb_MovFluidos WHERE idMovFluido='$id'");
            $row=$datos->fetch_assoc();
        }
    }
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <?php include('../base/head.php');?>
    </head>
    <body>
        <h4 align='center'>Datos de Combustible</h4>
        <form method='post'>
            <?php if($mov=='motores' and $id!='caldera' or $id=='motores'){?>
                    <div class="row">
                        <div class="small-6 medium-4 large-2 columns">
                            <label>Lectura Inicial
                                <input type="text" name='lectIni' placeholder="Lectura Inicial" value='<?php echo $lectIni;?>'>
                            </label>
                        </div>
                        <div class="small-6 medium-4 large-2 columns">
                            <label>Lectura Final
                                <input type="text" name='lectFin' placeholder="Lectura Final" autofocus="autofocus" value='<?php echo $lectFin;?>' required>
                            </label>
                        </div>
                    </div>
                    <div class="small-12 medium-8 large-4 columns">
                        <label>Comentario
                            <textarea placeholder="Comentario" name='comentario' value=''><?php echo $row['comentarios'];?></textarea>
                        </label>
                    </div>
                <?php }
            
              if($id=='caldera' or $mov=='caldera'){?>
                <div class="row">
                    <div class="small-5 medium-3 large-1 columns">
                        <label>Consumido por Caldera
                            <input type="text" name='c_caldera' placeholder="Consumo Caldera" autofocus="autofocus" value='<?php if($datos->num_rows>0){echo $row['litros'];}?>' required>
                        </label>
                    </div>
                    <div class="small-7 medium-5 large-3 columns">
                        <label>Comentario
                            <textarea placeholder="Comentario" name='comentario'><?php if($datos->num_rows>0){echo $row['comentarios'];}?></textarea>
                        </label>
                    </div>
                </div>
            <?php }?>
            <div align='center'><input class="button small button-radius btnOpcion" type='submit' name='ingresarComponente' value='Ingresar Datos'></div>
        </form>

        <?php $lIni = str_replace("-","",$_POST['lectIni']); $lFin = str_replace("-","",$_POST['lectFin']);
            if($id=='motores'){
                $verificar=$mysqli->query("SELECT * FROM tb_MovFluidos WHERE id_Tk='".$tk."' AND fecha='".$fecha."' and lecturaIni < 0");
                if(isset($_POST['ingresarComponente']) and $verificar->num_rows == 0){
                        $ingresar=$mysqli->query("INSERT INTO `tb_MovFluidos` VALUES ('','".$tk."','".$fecha."',NULL,NULL,'-".$lIni."','-".$lFin."',NULL,NULL,NULL,NULL,'".$_POST['comentario']."',now(),NULL)");
                } 
                if($ingresar){echo "<script>alert('Datos Ingresados!');window.parent._ocultarIframe();</script>";}
            }else{
                if($mov=='motores'){ 
                    if(isset($_POST['ingresarComponente'])){ 
                        $modificar=$mysqli->query("UPDATE `tb_MovFluidos` SET `lecturaIni`='-".$lIni."',`lecturaFin`='-".$lFin."',`comentarios`='".$_POST['comentario']."',`updated_`=now() WHERE `idMovFluido`='$id'");
                        //echo "UPDATE `tb_MovFluidos` SET `lecturaIni`='-".$lIni."',`lecturaFin`='-".$lFin."',`comentarios`='".$_POST['comentario']."',`updated_`=now() WHERE `idMovFluido`='$id'";
                    }
                    if($modificar){echo "<script>alert('Datos Modificados!');window.parent._ocultarIframe();</script>";}
                }
            }
            
            $caldera = str_replace(".","",$_POST['c_caldera']);
            if($id=='caldera'){
                $verificar=$mysqli->query("SELECT * FROM tb_MovFluidos WHERE id_Componente='".$tk."' AND fecha='".$fecha."'");
                if(isset($_POST['ingresarComponente']) and $verificar->num_rows == 0){
                    $ingresar=$mysqli->query("INSERT INTO `tb_MovFluidos` VALUES ('','0','".$fecha."','".$caldera."',NULL,NULL,NULL,NULL,NULL,NULL,'".$tk."','".$_POST['comentario']."',now(),NULL)");
                    echo "<script>var cambios='ok';</script>";
                }
                if($ingresar){echo "<script>alert('Datos Ingresados!');window.parent._ocultarIframe();</script>";}
            }else{ 
                if($mov=='caldera'){
                    if(isset($_POST['ingresarComponente'])){
                    $modificar=$mysqli->query("UPDATE `tb_MovFluidos` SET `litros`='".$caldera."',`comentarios`='".$_POST['comentario']."',`updated_`=now() WHERE `idMovFluido`='$id'");
                    }
                    if($modificar){echo "<script>var cambios='ok';alert('Datos Modificados!');window.parent._ocultarIframe();</script>";}
                }
            }
        ?>
        <script>
        function _enviarAlPadre(){
            window.parent._ocultarIframe();
        }
        </script>
        <?php mysqli_close($mysqli); include('../base/foot.php');?>
    </body>
</html>