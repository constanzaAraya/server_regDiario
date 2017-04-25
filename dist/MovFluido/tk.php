<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');

    $dia=$_GET['d'];
    $mov=$_GET['m'];
    $id=$_GET['i'];
    $tk=$_GET['t'];

    $fecha=$año.'-'.$mes.'-'.$dia;
    if($mov=='edita'){
        $buscar=$mysqli->query("SELECT * FROM tb_MovFluidos WHERE idMovFluido='$id'");
        $row=$buscar->fetch_assoc();
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
        <h4 align='center'>Datos de Estanques</h4>
        <form method='post'><?php /*
            <fieldset class="fieldset">
                    <div class='row'>
                        <div class="large-6 columns">
                            <legend>Tipo de Ingreso</legend>
                            <input type="radio" name="movimiento" onclick="submit()" value="1" id="D" <?php if($_POST['movimiento']=='1'){echo "checked";}?> required><label for="D">Descarga</label>
                            <input type="radio" name="movimiento" onclick="submit()" value="2" id="C" <?php if($_POST['movimiento']=='2'){echo "checked";}?> required><label for="C">Consumo</label>
                        </div>
                    </div>
            </fieldset>*/?>
            <div class="row">
                <div class="small-5 medium-3 large-1 columns">
                    <label>Nivel tk (%)
                        <input type="text" value="<?php echo $row['nivelTk'];?>" autofocus="autofocus" placeholder='Nivel: 0,32' name='nivel' required>
                    </label>
                </div>
                <div class='small-7 medium-8 large-8 columns'>
                    <label>Comentario
                        <textarea placeholder="Comentario" name='comenta' rows='1'><?php echo $row['comentarios'];?></textarea>
                    </label>
                </div>
            </div>
            <div align='center'><input class="button small button-radius btnOpcion" type='submit' name='ingresar' value='Ingresar Datos'></div>
        </form>
        <?php $nivel = str_replace(',','.',$_POST['nivel']); $nivel = str_replace("-","",$nivel); 
            if($mov=='edita'){
                if(isset($_POST['ingresar'])){
                    $modifica=$mysqli->query("UPDATE `tb_MovFluidos` SET `nivelTk`='".$nivel."',`comentarios`='".$_POST['comenta']."',`updated_`=now() WHERE `idMovFluido`='$id'");
                }
                if($modifica){echo "<script>alert('Datos Modificados!');window.parent._ocultarIframe();</script>";}
            }else{
                $verificar=$mysqli->query("SELECT * FROM tb_MovFluidos WHERE id_Componente='".$tk."' AND fecha='".$fecha."'");
                if(isset($_POST['ingresar'])){  
                    $insert=$mysqli->query("INSERT INTO `tb_MovFluidos` VALUES ('','".$tk."','".$fecha."',NULL,'".$nivel."',NULL,NULL,NULL,NULL,NULL,NULL,'".$_POST['comenta']."',now(),NULL)");
                }
                if($insert){echo "<script>alert('Datos Ingresados!');window.parent._ocultarIframe();</script>";}
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