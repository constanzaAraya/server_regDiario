<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');
    
    $dia=$_GET['d'];
    $idComp=$_GET['i'];
    $idMov=$_GET['id'];
    $movim=$_GET['m'];
    
    $fecha=$año.'-'.$mes.'-'.$dia;
    $ayer=date('Y-m-d', strtotime($fecha) - 3600);

    $datosAnteriores=$mysqli->query("SELECT hr_fin FROM tbl_HrsOperacion WHERE fecha='$ayer' AND id_Componente='$idComp'");
    if($datosAnteriores->num_rows > 0){
        $rows=$datosAnteriores->fetch_assoc();
        $hrIni=$rows['hr_fin'];
    }else{
        $buscar=$mysqli->query("SELECT * FROM tbl_HrsOperacion WHERE idHoras='$idMov'");
        if($buscar->num_rows>0){
            $row=$buscar->fetch_assoc();
            $hrIni=$row['hr_ini'];
            $hrFin=$row['hr_fin'];
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
        <h4 align='center'>Horas de máquina por motor</h4>
        <form method='post'>
            <div class="row">
                <div class="small-6 medium-4 large-2 columns">
                    <label>Horometro Inicial
                        <input type="number" size='5' name='hrIni' placeholder="Horometro Inicial" value='<?php if($hrIni){echo $hrIni;}else{echo $_POST['hrIni'];}?>'>
                    </label>
                </div>
                <div class="small-6 medium-4 large-2 columns">
                    <label>Horometro Final
                        <input type="number" size='5' name='hrFin' placeholder="Horometro Final" autofocus="autofocus" value='<?php if($hrFin){echo $hrFin;}else{echo $_POST['hrFin'];}?>' step="any" required> 
                    </label>
                </div>
            </div>
            <div class="row">
                <label>Comentario
                    <textarea placeholder="Comentario" name='comentario' value=''><?php if($buscar->num_rows>0){echo $row['comentario'];}else{echo $_POST['comentario'];}?></textarea>
                </label>
            </div>
            <div align='center'><input class="button small button-radius btnOpcion" type='submit' name='ingresar' value='Ingresar Datos'></div>
        </form>
        <?php if($movim=='ingresa'){
                $verificar=$mysqli->query("SELECT * FROM tbl_HrsOperacion WHERE id_Componente='".$idComp."' AND fecha='".$fecha."'");
                if(isset($_POST['ingresar']) and $verificar->num_rows == 0){
                    $ingresar=$mysqli->query("INSERT INTO `tbl_HrsOperacion` VALUES ('','".$idComp."','".$fecha."','".$_POST['hrIni']."','".$_POST['hrFin']."','".$_POST['comentario']."',now(),NULL)");
                }
                if($ingresar){echo "<script>alert('Datos Ingresados!');window.parent._ocultarIframe();</script>";}
            }else{
                if($movim=='edita'){
                    if(isset($_POST['ingresar'])){
                        $modificar=$mysqli->query("UPDATE `tbl_HrsOperacion` SET `hr_ini`='".$_POST['hrIni']."',`hr_fin`='".$_POST['hrFin']."',`comentario`='".$_POST['comentario']."',`updated`=now() WHERE `idHoras`='".$idMov."'");
                    }
                    if($modificar){echo "<script>alert('Datos Modificados!');window.parent._ocultarIframe();</script>";}
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