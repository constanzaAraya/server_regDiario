<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');
    if($tiempo_transcurrido >= 2700 or $acceso==''){  
        session_start(); 
        echo "<script>alert('Su sesion a expirado. Vuelva a iniciar sesion.')</script>";
        echo "<script>window.parent.location.href='../../session/closeLog.php';</script>";
        session_destroy(); 
        exit(); 
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
    <div class="large-12 columns">
        <fieldset class="fieldset">
            <div class='row'>
                <div class="small-2 columns">
                   <form method="post" name="form1">
                    <input type="hidden" name="consulta" value="<?php echo "SELECT tbl_Fluidos.nombre as Fluido, tb_MovFluidos.fecha as Fecha, tb_MovFluidos.comentarios as Comentario FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk JOIN tbl_Fluidos ON tbl_Tk.id_Fluido=tbl_Fluidos.idFluido WHERE comentarios <> '' AND tbl_Fluidos.id_TipoFluido='2'";?>">
                    <a href="javascript:document.form1.submit()" aria-selected="true">Agua</a>
                   </form>
                </div>
                <div class="small-2 columns">
                   <form method="post" name="form2">
                    <input type="hidden" name="consulta" value="<?php echo "SELECT tbl_Fluidos.nombre as Fluido, tb_MovFluidos.fecha as Fecha, tb_MovFluidos.comentarios as Comentario FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk JOIN tbl_Fluidos ON tbl_Tk.id_Fluido=tbl_Fluidos.idFluido WHERE comentarios <> '' AND tbl_Fluidos.id_TipoFluido='3'";?>">
                    <a href="javascript:document.form2.submit()">Aceite</a>
                   </form>
                </div>
                <div class="small-2 columns">
                   <form method="post" name="form3">
                    <input type="hidden" name="consulta" value="<?php echo "SELECT tbl_Fluidos.nombre as Fluido, tb_MovFluidos.fecha as Fecha, tb_MovFluidos.comentarios as Comentario FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk JOIN tbl_Fluidos ON tbl_Tk.id_Fluido=tbl_Fluidos.idFluido WHERE comentarios <> '' AND tb_MovFluidos.id_Tk='16' AND lecturaIni < 0";?>">
                   <a href="javascript:document.form3.submit()">Combustible</a>
                   </form>
                </div>
                <div class="small-2 columns">
                   <form method="post" name="form4">
                    <input type="hidden" name="consulta" value="<?php echo "SELECT tbl_Componentes.nombre as Motor, tb_MovFluidos.fecha as Fecha, tb_MovFluidos.comentarios as Comentario FROM tb_MovFluidos JOIN tbl_Componentes ON tb_MovFluidos.id_Componente=tbl_Componentes.idComponente WHERE comentarios <> '' AND tb_MovFluidos.id_Tk='17' AND lecturaIni < 0 AND id_Componente IN (1,2,3,4,5,6) ORDER BY fecha";?>">
                    <a href="javascript:document.form4.submit()">HFO</a>
                   </form>
                </div>
                <div class="small-2 columns">
                   <form method="post" name="form5">
                    <input type="hidden" name="consulta" value="<?php echo "SELECT DISTINCT(tbl_Componentes.nombre) AS Motor, tbl_HrsOperacion.fecha as Fecha, tbl_HrsOperacion.comentario as Comentario FROM tbl_TipoComponentes JOIN tbl_Componentes ON tbl_TipoComponentes.idTipoComponente=tbl_Componentes.id_TipoComponente JOIN tbl_HrsOperacion ON tbl_Componentes.idComponente=tbl_HrsOperacion.id_Componente WHERE tbl_TipoComponentes.idTipoComponente='1' AND tbl_HrsOperacion.comentario <> ''";?>">
                    <a href="javascript:document.form5.submit()">Horometro</a>
                   </form>
                </div>
                <div class="small-2 columns">
                   <form method="post" name="form6">
                    <input type="hidden" name="consulta" value="<?php echo "SELECT tbl_Tk.nombre AS Fluido, fecha as Fecha, comentarios AS Comentario FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk WHERE id_Tk IN (1,2,3,4,5,6) AND comentarios <> ''";?>">
                    <a href="javascript:document.form6.submit()">Estanque</a>
                   </form>
                </div>
            </div>
        </fieldset>
    </div>

    <?php $sql=$_POST['consulta'];
                function displayTable($sql){
                        include('../connection/connBD.php');
                        if(isset($sql)){
                        $result=$mysqli->query($sql);
                        $rawdata = array();//guardamos en un array multidimensional todos los datos de la consulta
                        $i=0;
                        
                        while($row = $result->fetch_array()){
                            $rawdata[$i] = $row;//almacena las filas de la consulta
                            $i++;
                        }
                        $close = mysqli_close($mysqli);
                        
                        //DIBUJAMOS LA TABLA
                        echo '<div class="row collapse"><div class="small-12 columns"><table style="text-align:center;">';
                        $columnas = count($rawdata[0])/2;
                        //echo $columnas;
                        $filas = count($rawdata);
                        //echo "<br>".$filas."<br>";
                                                                
                        //Añadimos los titulos
                        for($i=1;$i<count($rawdata[0]);$i=$i+2){
                            next($rawdata[0]);
                            echo "<th><b>".key($rawdata[0])."</b></th>";
                            next($rawdata[0]);
                        }
                                                            
                        for($i=0;$i<$filas;$i++){
                            echo "<tr>";
                            for($j=0;$j<$columnas;$j++){
                                echo "<td>".$rawdata[$i][$j]."</td>";
                            }
                            echo "</tr>";
                        }
                        echo '</table></div>
                        </div>';
                        }
                    }
                    echo displayTable($sql);
                ?>
        <?php mysqli_close($mysqli); include('../base/foot.php');?>
    </body>
</html>