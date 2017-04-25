<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');   
    
    $dia=$_GET['d'];
    $tk=$_GET['t'];
    $idMov=$_GET['i'];
    $mov=$_GET['m'];
    $fl=$_GET['f'];
    if($mov=='Diesel' or $fl=='Diesel'){
        $unidad="Lts.";
    }else{
        $unidad="Kgs.";
    }
    
    $fecha=$año.'-'.$mes.'-'.$dia;
    $ayer=date('Y-m-d', strtotime($fecha) - 3600);//ferificar datos

    if($mov!=''){
        $buscar=$mysqli->query("SELECT * FROM tb_MovFluidos WHERE idMovFluido='$idMov'");
        if($buscar->num_rows>0){
            $row=$buscar->fetch_assoc();
            $lectIni=$row['lecturaIni'];
            $lectFin=$row['lecturaFin'];
        }
    }else{ 
        $datosAnteriores=$mysqli->query("SELECT idMovFluido, lecturaFin FROM tb_MovFluidos WHERE year(fecha)='$año' AND month(fecha)='$mes' AND id_Tk='".$tk."' AND lecturaIni > 0 ORDER BY idMovFluido DESC limit 1");
        if($datosAnteriores->num_rows>0){
            $rows=$datosAnteriores->fetch_assoc();
            $lectIni=str_replace("-","",$rows['lecturaFin']);
        }
    }    
    /*pattern='' title="No se permite el ingreso de decimales!"*/
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <?php include('../base/head.php');?>
    </head>
    <body>
        <h4 align='center'>Descarga de combustible</h4>
        <form method='post'>        
            <div class="row">
                <div class="small-6 medium-4 large-2 columns">
                    <label>Lectura Inicial
                        <input type="number" size='5' name='lectIni' placeholder="Lectura Inicial" pattern='[,-]' value='<?php if($lectIni){echo $lectIni;}else{echo $_POST['lectIni'];}?>' step="any" required>
                    </label>
                </div>
                <div class="small-6 medium-4 large-2 columns">
                    <label>Lectura Final
                        <input type="number" size='5' name='lectFin' autofocus="autofocus" placeholder="Lectura Final" pattern='[,-]' value='<?php if($lectFin){echo $lectFin;}else{echo $_POST['lectFin'];}?>' step="any" required> 
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="small-6 medium-4 large-2 columns">
                    <label><?php echo $unidad;?> Camion
                        <input type="number" name='camion' placeholder="<?php echo $unidad;?> Camion" value='<?php if($row['kg_lt_camion']!=''){echo $row['kg_lt_camion'];}else{echo $_POST['camion'];}?>'>
                    </label>
                </div>
                   <div class="small-6 medium-4 large-2 columns">
                     <label>Nº Guia
                        <input type="text" name='guia' placeholder="Nº de Guia" value='<?php if($row['nGuia']!=''){echo $row['nGuia'];}else{echo $_POST['guia'];}?>'>
                    </label>
               </div>
            </div>
            <?php /*if($mov=='Diesel' or $mov=='HFO'){*/?>
            <div class="row">
                <div class="small-5 medium-4 large-2 columns">
                    <label>Valor Lt.
                        <input type="number" name='valor' placeholder="Valor Lts." pattern='[,.-]' value='<?php if($row['valorLt']!=''){echo $row['valorLt'];}else{echo $_POST['valor'];}?>'>
                    </label>
                </div>
                <div class="small-7 medium-4 large-2 columns">
                    <label>Comentario
                        <textarea placeholder="Comentario" name='comentario'><?php if($row['comentarios']!=''){echo $row['comentarios'];}else{echo $_POST['comentario'];}?></textarea>
                    </label>
                </div>   
            </div>
            <?php /*}*/?>
            <div class='row'><div align='center'><input class="button small button-radius btnOpcion" type='submit' name='ingresarComponente' value='Ingresar Datos'></div></div>
        </form>
        <?php  $lIni = str_replace("-","",$_POST['lectIni']); $lFin = str_replace("-","",$_POST['lectFin']); $camion = str_replace(".","",$_POST['camion']);
            if(isset($idMov)){
                if(isset($_POST['ingresarComponente'])){
                    $modifica=$mysqli->query("UPDATE `tb_MovFluidos` SET `lecturaIni`='".$lIni."',`lecturaFin`='".$lFin."',`nGuia`='".$_POST['guia']."',`kg_lt_camion`='".$camion."',`valorLt`='".$_POST['valor']."',`comentarios`='".$_POST['comentario']."',`updated_`=now() WHERE `idMovFluido`='$idMov'");
                }
                if($modifica){echo "<script>alert('Datos Modificados!');window.parent._ocultarIframe();</script>";}
            }else{
                if(isset($_POST['ingresarComponente'])){
                    $insert=$mysqli->query("INSERT INTO `tb_MovFluidos` VALUES ('','$tk','".$fecha."',NULL,NULL,'".$lIni."', '".$lFin."','".$_POST['guia']."','".$camion."',NULL,NULL,'".$_POST['comentario']."',now(),NULL)");
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