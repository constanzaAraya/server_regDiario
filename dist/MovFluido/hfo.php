<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');
    
    $dia=$_GET['d'];
    $idComp=$_GET['i'];
    $idMov=$_GET['id'];
    $movim=$_GET['m'];

    $fecha=$año.'-'.$mes.'-'.$dia;
    $ayer=date('Y-m-d', strtotime($fecha) - 3600);

    $datosAnteriores=$mysqli->query("SELECT lecturaFin FROM tb_MovFluidos WHERE fecha='$ayer' AND id_Componente='$idComp'");
    if($datosAnteriores->num_rows > 0){
        $rows=$datosAnteriores->fetch_assoc();
        $lectIni=str_replace("-","",$rows['lecturaFin']);
    }else{
        $buscar=$mysqli->query("SELECT * FROM tb_MovFluidos WHERE idMovFluido='$idMov'");
        if($buscar->num_rows>0){
            $row=$buscar->fetch_assoc();
            if($row['lecturaIni'] < 0 or $row['lecturaFin'] < 0){
                $lectura=$mysqli->query("SELECT id_Tk, substring_index(lecturaIni,'-',-1) AS lectIni, substring_index(lecturaFin,'-',-1) AS lectFin FROM tb_MovFluidos WHERE idMovFluido='$idMov'");
                $row_lect=$lectura->fetch_assoc();
                $lectIni=$row_lect['lectIni'];
                $lectFin=$row_lect['lectFin'];
            }else{ 
                $lectIni=$row['lecturaIni'];
                $lectFin=$row['lecturaFin'];
            }
            $tk=$row['id_Tk'];
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
        <h4 align='center'>Consumo de Combustible HFO</h4>
        <form method='post'>
            <div class="row">
                <div class="small-6 medium-4 large-2 columns">
                    <label>Lectura Inicial
                        <input type="number" size='5' name='lectIni' placeholder="Lectura Inicial" value='<?php if($lectIni){echo $lectIni;}else{echo $_POST['lectIni'];}?>'>
                    </label>
                </div>
                <div class="small-6 medium-4 large-2 columns">
                    <label>Lectura Final
                        <input type="number" size='5' name='lectFin' placeholder="Lectura Final" autofocus="autofocus" value='<?php if($lectFin){echo $lectFin;}else{echo $_POST['lectFin'];}?>' step="any" required> 
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="small-12">
                    <label>Comentario
                        <textarea placeholder="Comentario" name='comentario' value=''><?php if($buscar->num_rows>0){echo $row['comentarios'];}else{echo $_POST['comentario'];}?></textarea>
                    </label>
                </div>
            </div>
            <div align='center'><input class="button small button-radius btnOpcion" type='submit' name='ingresarComponente' value='Ingresar Datos'></div>
        </form>
        <?php if($movim=='ingresa'){ 
                $verificar=$mysqli->query("SELECT * FROM tb_MovFluidos WHERE id_Componente='".$idComp."' AND fecha='".$fecha."'");
                if(isset($_POST['ingresarComponente']) and $verificar->num_rows == 0){
                    $ingresar=$mysqli->query("INSERT INTO `tb_MovFluidos` VALUES ('','17','".$fecha."',NULL,NULL,'-".$_POST['lectIni']."','-".$_POST['lectFin']."',NULL,NULL,NULL,'".$idComp."','".$_POST['comentario']."',now(),NULL)");
                }
                if($ingresar){echo "<script>alert('Datos Ingresados!');window.parent._ocultarIframe();</script>";}
            }else{
                if($movim=='edita'){
                    if(isset($_POST['ingresarComponente'])){
                        $modificar=$mysqli->query("UPDATE `tb_MovFluidos` SET `lecturaIni`='-".$_POST['lectIni']."',`lecturaFin`='-".$_POST['lectFin']."',`comentarios`='".$_POST['comentario']."',`updated_`=now() WHERE `idMovFluido`='$idMov'");
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