<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');
    if($tiempo_transcurrido >= 2700 or $acceso==''){  
        session_start(); 
        echo "<script>alert('Su sesion a expirado. Vuelva a iniciar sesion.')</script>";
        echo "<script>window.parent.location.href='../../session/closeLog.php';</script>";
        session_destroy(); 
        exit(); 
    }

    if($_GET['a']){echo "<script>var popup = new Foundation.Reveal($('#modalDiesel'));
          popup.close(); window.location = 'ingresoCo.php';</script>";}
    
    $idFl=base64_decode($_GET['f']);
    $tipos_cons="SELECT tbl_Tk.idTk, tbl_Fluidos.nombre FROM tbl_Fluidos JOIN tbl_Tk ON tbl_Fluidos.idFluido=tbl_Tk.id_Fluido WHERE id_TipoFluido='$idFl' AND ingreso='0'";
    $tipos=$mysqli->query($tipos_cons);
    $cant=$tipos->num_rows;
    $año_mes=$año.'-'.$mes;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <?php include('../base/head.php');?>
    </head>
    <body>
        <div class="small-12 columns table-scroll">
            <table border="1">
                <tr align='center'>
                    <th>
                    <?php //if($acceso=='1'){?>
                        <form method="post" action="Exportar_excel/exp_combustible.php">
                            <input type="hidden" name="tipos" value="<?php echo $tipos_cons;?>">
                            <input type="hidden" name="idFluido" value="<?php echo $idFl;?>">
                            <button type="submit" name="submit" value=""/><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </form>
                    <?php //}?>
                    </th>
                    <?php while($datos=$tipos->fetch_assoc()){?> 
                    <th colspan='9'><?php echo $name[]=$datos['nombre']; $idTK[]=$datos['idTk'];?></th><?php }?>
                </tr>
                <form method='post'>
                <tr align='center'>
                    <th><?php echo $mes.'_'.$año;?></th>
                    <?php for($x=0;$x<$cant;$x++){
                    if($name[$x] == "Diesel"){?>
                    <th>Lts. Recibido</th>
                    <th>Nivel Inicial</th>
                    <th>Nivel Final</th>
                    <th></th>
                    <th>Consumo Motores(Lts)</th>
                    <th>Consumo Caldera(Lts)</th>
                    <th></th>
                    <th>Consumo Diario(Lts)</th>
                    <th>Stock (Lts)</th>
                <?php }else{
                    if($name[$x] == "HFO"){?>
                    <th>Lts. Recibidos</th>
                    <th>Ton</th>
                    <th>Consumo Diario(Lts)</th>
                    <th>Ton</th>
                    <th>Stock Lts.</th>
                    <th>Ton</th>
                <?php }}
                }?>
                </tr>
                <?php
                 for($a=1;$a<=$ultimoDia;$a++){?>
                <tr align='center'>
                    <?php for($b=0;$b<$cant;$b++){
                     if($b==0){?>
                    <td><?php echo $a;?></td><?php }?>
                    <?php if($name[$b]=='Diesel'){
                                /*$valores=$mysqli->query("SELECT Recibido, id1, lecturaIni, lecturaFin, consumoM, id2, cCaldera, consumoM-cCaldera AS ConsumoDiario, stock2, comentarios FROM (
                                    SELECT idMovFluido AS id1, lecturaIni, lecturaFin, lecturaFin-lecturaIni AS consumoM, comentarios,
                                        (SELECT litros FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Componente='9') AS cCaldera,
                                        (SELECT idMovFluido FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Componente='9') AS id2,
    	                                (SELECT Valor FROM tbl_ValoresIniciales WHERE id_Tk='$idTK[$b]' AND id_Componente='9') AS valorInicial,
                                        CASE
                                            WHEN (SELECT COUNT(*) FROM tb_MovFluidos WHERE id_Tk='$idTK[$b]' AND fecha BETWEEN '2017-01-01' AND '$año_mes-$a' AND lecturaIni < 0) > 1 
                                            THEN (((SELECT Valor FROM tbl_ValoresIniciales WHERE id_Tk='$idTK[$b]')+round(sum(lecturaFin-lecturaIni),0))+(SELECT sum((round(lecturaFin-lecturaIni,2))*1000) FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' AND lecturaIni > 0))
                                            WHEN (SELECT COUNT(*) FROM tb_MovFluidos WHERE id_Tk='$idTK[$b]' AND fecha BETWEEN '2017-01-01' AND '$año_mes-$a' AND lecturaIni < 0) <= 1 
                                            THEN (SELECT Valor FROM tbl_ValoresIniciales WHERE id_Tk='$idTK[$b]')
                                        END AS stock2,
                                        (SELECT sum(kg_lt_camion) FROM `tb_MovFluidos` WHERE id_Tk='$idTK[$b]' AND fecha='$año_mes-$a' AND lecturaIni > 0) AS Recibido
                                    FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' and lecturaIni < 0 
                                    ) AS dat LIMIT 1");*/
                                    $valores=$mysqli->query("SELECT Recibido, id1, lecturaIni, lecturaFin, consumoM, id2, cCaldera, consumoM-cCaldera AS ConsumoDiario, valorInicial, comentarios FROM (
                                    SELECT idMovFluido AS id1, lecturaIni, lecturaFin, lecturaFin-lecturaIni AS consumoM, comentarios,
                                        (SELECT litros FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Componente='9') AS cCaldera,
                                        (SELECT idMovFluido FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Componente='9') AS id2,
    	                                (SELECT Valor FROM tbl_ValoresIniciales WHERE id_Tk='$idTK[$b]' AND id_Componente='9') AS valorInicial,
                                        (SELECT sum(kg_lt_camion) FROM `tb_MovFluidos` WHERE id_Tk='$idTK[$b]' AND fecha='$año_mes-$a' AND lecturaIni > 0) AS Recibido
                                    FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' and lecturaIni < 0 
                                    ) AS dat LIMIT 1");
                                $row=$valores->fetch_assoc();
                                $total=$valores->num_rows;
                            ?>
                    <td><?php /*$recibo=$mysqli->query("SELECT sum(kg_lt_camion) AS Recibido FROM `tb_MovFluidos` WHERE id_Tk='$idTK[$b]' AND fecha='$año_mes-$a' AND lecturaIni > 0"); $recibo_=$recibo->fetch_assoc();
                        if($recibo_['Recibido']!=''){echo number_format($recibo_['Recibido'],0,',','.'); }*/?>
                        
                        <?php if($row['Recibido']!=0){ echo number_format($row['Recibido'],0,',','.');}?></td>
                    <td><?php if($total > 0){echo str_replace("-","",number_format($row['lecturaIni'],0,',','.'));}?></td>
                    <td><?php if($total > 0){echo str_replace("-","",number_format($row['lecturaFin'],0,',','.'));}?></td>
                    <td>
                        <div class="row">     
                            <div class="small-1 columns">
                                <?php if($row['comentarios']!=''){?><span data-tooltip aria-haspopup="true" class="has-tip" data-disable-hover="false" tabindex="1" title="<?php echo $row['comentarios'];?>"><i class="fa fa-cloud" aria-hidden="true"></i></span><?php }?>
                            </div>
                            <div class="small-1 columns"><?php if($acceso!='4'){//4:solo visualiza
                            if(($a<=$ayer) AND ( $total > 0)){?>
                                <a align='right'><button onclick="ingresar_datos(this); return false;" title='Editar Consumo Motores!' day="<?php echo $a;?>" id='<?php echo $row['id1'];?>' movim="motores" value="Editar" />
                                <i class="fa fa-pencil" aria-hidden="true"></i></button></a>
                                <?php }else{ if($a<=$ayer){?>
                                    <button class="button-radius" title='Ingresar Consumo Motores!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" id='motores' tk="<?php echo $idTK[$b];?>" value="Ingresar"/><i class="fa fa-plus-square-o" aria-hidden="true"></i></button>
                                <?php }}}?>
                            </div>
                        </div>
                    </td>
                    <td><?php if($total > 0){echo str_replace("-","",number_format($row['consumoM'],0,',','.'));}?></td>
                    <td><?php $consumoCaldera=$mysqli->query("SELECT id2, cCaldera, comentarios from (
                                    SELECT comentarios, 
                                        (SELECT litros FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Componente='9') AS cCaldera,
                                        (SELECT idMovFluido FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Componente='9') AS id2 
                                    FROM tb_MovFluidos 
                                    WHERE fecha='$año_mes-$a' and litros >=0
                                ) AS dat LIMIT 1");
                                $row_consumoCaldera=$consumoCaldera->fetch_assoc();?>
                                <div class="row">
                            <div class="small-1 columns"><?php if(($a<=$ayer) and ($row_consumoCaldera['cCaldera'] != '')){ echo number_format($row_consumoCaldera['cCaldera'],0,',','.'); }?></div>
                            <div class="small-1 columns">
                                <?php if($row_consumoCaldera['comentarios']!=''){?><span data-tooltip aria-haspopup="true" class="has-tip" data-disable-hover="false" tabindex="1" title="<?php echo $row_consumoCaldera['comentarios'];?>"><i class="fa fa-cloud" aria-hidden="true"></i></span><?php }?>
                            </div>
                        </div>  
                    </td>
                    <td><?php if($acceso!='4'){//4:solo visualiza
                        if(($a<=$ayer) and ($row_consumoCaldera['cCaldera'] != '')){ ?>
                        <a align='right'><button onclick="ingresar_datos(this); return false;" title='Editar Consumo Caldera!' day="<?php echo $a;?>" id='<?php echo $row_consumoCaldera['id2'];?>' movim="caldera" value="Editar" /><i class="fa fa-pencil" aria-hidden="true"></i></button></a>
                        <?php }else{
                            if($a<=$ayer){?>
                            <button class="button-radius" title='Ingresar Consumo Caldera!' onclick="ingresar_datos(this); return false;" day="<?php echo $a;?>" id='caldera' tk="9" value="Ingresar Consumo"/><i class="fa fa-plus-square-o" aria-hidden="true"></i></button>
                        <?php }}
                        }?>
                    </td>
                    <td><?php if($total > 0){ echo str_replace("-","",number_format($row['ConsumoDiario'],0,',','.')); }?></td>    
                    <td><?php /*echo number_format($row_consumo['stock2'],0,',','.');*/?></td>
                    <?php } 
                        if($name[$b]=='HFO'){
                        /*$consumoDia=$mysqli->query("SELECT Recibido, (Recibido*densidad)/1000000 AS Ton1, ConsumoDiario, Ton2, Stock FROM (
                            SELECT 
                                round(sum(lecturaFin-lecturaIni),0) ConsumoDiario, 
                                (SELECT sum((round(lecturaFin-lecturaIni,2))*1000) FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' AND lecturaIni > 0) AS Recibido,
                                round(((sum(lecturaFin-lecturaIni))*(SELECT densidad*1000 FROM tbl_Fluidos WHERE nombre='$name[$b]'))/1000000,4) AS Ton2,
                                round((SELECT densidad FROM tbl_Fluidos WHERE nombre='$name[$b]')*1000,1) AS densidad,  
                                CASE
                                    WHEN (SELECT COUNT(*) FROM tb_MovFluidos WHERE id_Tk='$idTK[$b]' AND fecha BETWEEN '2017-01-01' AND '$año_mes-$a' AND lecturaIni < 0) > 6 
                                    THEN (((SELECT Valor FROM tbl_ValoresIniciales WHERE id_Tk='$idTK[$b]')+round(sum(lecturaFin-lecturaIni),0))+(SELECT sum((round(lecturaFin-lecturaIni,2))*1000) FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' AND lecturaIni > 0))
                                    WHEN (SELECT COUNT(*) FROM tb_MovFluidos WHERE id_Tk='$idTK[$b]' AND fecha BETWEEN '2017-01-01' AND '$año_mes-$a' AND lecturaIni < 0) <= 6 
                                    THEN (SELECT Valor FROM tbl_ValoresIniciales WHERE id_Tk='$idTK[$b]')
                                END AS Stock
                                FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk
                                WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' AND lecturaIni < 0
                                )as datos");*/

                                $consumoDia=$mysqli->query("SELECT Recibido, (Recibido*densidad)/1000000 AS Ton1, ConsumoDiario, Ton2, Stock FROM (
                                SELECT 
                                round(sum(lecturaFin-lecturaIni),0) as ConsumoDiario, 
                                (SELECT round(sum((lecturaFin-lecturaIni)*1000)) FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' AND lecturaIni > 0) AS Recibido,
                                round(((sum(lecturaFin-lecturaIni))*(SELECT densidad*1000 FROM tbl_Fluidos WHERE nombre='$name[$b]'))/1000000,4) AS Ton2,
                                round((SELECT densidad FROM tbl_Fluidos WHERE nombre='$name[$b]')*1000,1) AS densidad,  
                                CASE
                                    WHEN (SELECT COUNT(*) FROM tb_MovFluidos WHERE id_Tk='$idTK[$b]' AND month(fecha)='$mes' AND year(fecha)='$año' AND lecturaIni > 0) > 1 
                                    THEN ((SELECT round(sum((lecturaFin-lecturaIni)*1000)) AS Recibido FROM tb_MovFluidos WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' AND lecturaIni > 0)-round(sum(lecturaFin-lecturaIni),0))+(SELECT Valor FROM tbl_ValoresIniciales WHERE id_Tk='$idTK[$b]')
                                    WHEN (SELECT COUNT(*) FROM tb_MovFluidos WHERE id_Tk='$idTK[$b]' AND month(fecha)='$mes' AND year(fecha)='$año' AND lecturaIni > 0) <= 1 
                                    THEN (SELECT Valor FROM tbl_ValoresIniciales WHERE id_Tk='$idTK[$b]')
                                END AS Stock    
                                FROM tb_MovFluidos JOIN tbl_Tk ON tb_MovFluidos.id_Tk=tbl_Tk.idTk
                                WHERE fecha='$año_mes-$a' AND id_Tk='$idTK[$b]' AND lecturaIni < 0 LIMIT 1
                                )as datos");

                                $row_consumo=$consumoDia->fetch_assoc();
                                $tot=$consumoDia->num_rows;
                                ?>
                    <td><?php if($row_consumo['Recibido']!=0){ echo number_format($row_consumo['Recibido'],0,',','.');}
                    /*$recibo2=$mysqli->query("SELECT sum(lecturaFin-lecturaIni)*1000 AS Recibido FROM `tb_MovFluidos` WHERE id_Tk='$idTK[$b]' AND fecha='$año_mes-$a' AND lecturaIni > 0");
                    $recibo_2=$recibo2->fetch_assoc();
                        if($recibo_2['Recibido']!=''){ echo number_format($recibo_2['Recibido'],0,',','.'); }*/?>
                    </td>
                    <td><?php if($a<=$ayer){echo number_format($row_consumo['Ton1'],4,',','.');}?></td>
                    <td><?php if($row_consumo['ConsumoDiario']!=''){echo str_replace("-","",number_format($row_consumo['ConsumoDiario'],0,',','.'));}
                        else{?>
                    <?php if($acceso!='4'){//4:solo visualiza
                    if($a<=$ayer){?><a href="ingresoHFO.php?c=<?php echo base64_encode('motor');?>" class="button-radius" title='Ingresar Consumo HFO!'/><i class="fa fa-plus-square-o" aria-hidden="true"></i></a>
                    <?php }}}?></td>
                    <td><?php if($a<=$ayer){echo str_replace("-","",number_format($row_consumo['Ton2'],4,',','.'));}?></td>
                    <td><?php /*if($tot==1){echo number_format($row_consumo['Stock'],0,',','.');}*/?></td>
                    <td></td>
                    <?php }?>
                    <?php }?>
                </tr>
                <?php }?>
                </form>
            </table>
        </div>

        <div class="small reveal pop" id="modalDiesel" data-reveal>
            <iframe frameBorder="0" src="" id="if2" width="100%" height="350px"></iframe>
            <button class="close-button" data-close aria-label="Close modal" type="button"><i class="fa fa-window-close" aria-hidden="true"></i></button>
        </div>

        <script type="text/javascript">
            function ingresar_datos(obj) {
                var mov=obj.getAttribute("movim");
                if(mov){
                    var id=obj.getAttribute("id");
                    var dia=obj.getAttribute("day");
                    
                    $('#modalDiesel').removeClass('hide');
                    $('#if2').attr('src', "co.php?i="+id+"&d="+dia+"&m="+mov+"");
                    var popup = new Foundation.Reveal($('#modalDiesel'));
                    popup.open();
                }
                else{
                    var id=obj.getAttribute("id");
                    var dia=obj.getAttribute("day");
                    var tk=obj.getAttribute("tk");

                    $('#modalDiesel').removeClass('hide');
                    $('#if2').attr('src', "co.php?i="+id+"&d="+dia+"&t="+tk+"");
                    var popup = new Foundation.Reveal($('#modalDiesel'));
                    popup.open();
                }
                return false;
            }
            function _ocultarIframe(){
                    $('#modalDiesel').foundation('destroy');
                    window.location='ingresoCo.php?f=<?php echo base64_encode(1);?>';
                    setTimeout('redireccionar()', 0);
            }
        </script>
        <?php mysqli_close($mysqli); include('../base/foot.php');?>
    </body>
</html>