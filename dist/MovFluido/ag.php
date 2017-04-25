<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');
    
    $fecha=$año.'-'.$mes.'-'.$_GET['d'];
    $id=$_GET['i'];
    $tipoFluido=$_GET['f'];
    $mov=$_GET['m'];
    $tk=$_GET['t'];
    
    $fluido=$mysqli->query("SELECT * FROM `tbl_TipoFluidos` WHERE idTipoFluido='$tipoFluido'");
    $row_fluido=$fluido->fetch_assoc();
    $name_fluido=$row_fluido['nombre'];

    if($id!=''){
        $buscar=$mysqli->query("SELECT * FROM tb_MovFluidos WHERE idMovFluido='$id'");
        $row=$buscar->fetch_assoc();
        $title='Modificar datos de '.$name_fluido.'';
    }else{$title='Ingresar datos de '.$name_fluido.'';}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <?php include('../base/head.php');?>
    </head>
    <body>
        <h4 align='center'><?php echo $title;?></h4>
        <form method='post'>
            <div class="row">
                <?php if($mov=='recibo'){?>
                <div class="small-5 medium-3 large-1 columns">
                    <label>Litros Recibidos
                        <input type="text" value="<?php echo $row['litros'];?>" autofocus="autofocus" placeholder='Litros' name="litro" required>
                    </label>
                </div>
                <?php }else{?>
                <div class="small-5 medium-3 large-1 columns">
                    <label>Nivel Tk
                        <input type="text" value="<?php echo str_replace("-","",$row['nivelTk']);?>" autofocus="autofocus" placeholder='Nivel TK' name="nivel" required>
                    </label>
                </div><?php }?>
                <div class='small-7 medium-8 large-8 columns'>
                    <label>Comentario
                        <textarea placeholder="Comentario" name='comenta' rows='1'><?php echo $row['comentarios'];?></textarea>
                    </label>
                </div>
            </div>
            <?php if($mov=='recibo'){ ?>
            <div class="medium-8 columns">
                <label>Valor
                    <input type="number" value="<?php echo $row['valorLt'];?>" placeholder='Valor Lt.' name='valor'>
                </label>
            </div>  
           <?php }?>
            <div align='center'><input class="button small button-radius btnOpcion" type='submit' name='ingresar' value='Ingresar Datos'></div>
        </form>

        <?php $nivel = str_replace(',','.',$_POST['nivel']); $nivel = str_replace("-","",$nivel); $litro = str_replace('.','',$_POST['litro']);
                if($id){//edita
                    if(isset($_POST['ingresar'])){
                        if($mov=='recibo'){
                            $modifica=$mysqli->query("UPDATE `tb_MovFluidos` SET `litros`='".$litro."',`valorLt`='".$_POST['valor']."',`comentarios`='".$_POST['comenta']."',`updated_`=now() WHERE `idMovFluido`='".$id."'");    
                        }else{
                            $modifica=$mysqli->query("UPDATE `tb_MovFluidos` SET `nivelTk`='-".$nivel."',`comentarios`='".$_POST['comenta']."',`updated_`=now() WHERE `idMovFluido`='".$id."'");
                        }
                        if($modifica){echo "<script>alert('Datos Modificados!');window.parent._ocultarIframe();</script>";
                      }
                    }
                }
                else{//ingresa
                    $verificacion=$mysqli->query("SELECT descarga, consumo FROM (
                        SELECT  (SELECT COUNT(*) FROM tb_MovFluidos WHERE id_Tk='".$tk."' AND litros > 0 AND fecha='".$fecha."') AS descarga, 
                            (SELECT COUNT(*) FROM tb_MovFluidos WHERE id_Tk='".$tk."' AND nivelTk <= 0 AND fecha='".$fecha."') AS consumo 
                        FROM tb_MovFluidos WHERE id_Tk='".$tk."' AND fecha='".$fecha."' 
                        )AS datos");
                    $row_verificacion=$verificacion->fetch_assoc();
                    if(isset($_POST['ingresar'])){
                        if($mov=='recibo' and $row_verificacion['descarga']<='0'){
                            $insert=$mysqli->query("INSERT INTO `tb_MovFluidos` VALUES ('','".$tk."','".$fecha."','".$litro."',NULL,NULL,NULL,NULL,NULL,'".$_POST['valor']."',NULL,'".$_POST['comenta']."',now(),NULL)");
                        }else{
                            if($row_verificacion['consumo']<='0'){
                                $insert=$mysqli->query("INSERT INTO `tb_MovFluidos` VALUES ('','".$tk."','".$fecha."',NULL,'-".$nivel."',NULL,NULL,NULL,NULL,NULL,NULL,'".$_POST['comenta']."',now(),NULL)");
                            }
                        }
                        if($insert){echo "<script>alert('Datos Ingresados!');window.parent._ocultarIframe();</script>";}
                    }
                }
         ?>
         <?php mysqli_close($mysqli); include('../base/foot.php');?>
         <script>
            function _enviarAlPadre(){
                window.parent._ocultarIframe();
            }
        </script>
    </body>
</html>