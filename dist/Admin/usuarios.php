<?php ini_set('session.use_only_cookies', true);
    include('../connection/connBD.php');
    if($acceso=='3' or $acceso=='' or $tiempo_transcurrido >= 2700){  
        session_start(); 
        echo "<script>alert('Su sesion a expirado. Vuelva a iniciar sesion.')</script>";
        echo "<script>window.parent.location.href='../../session/closeLog.php';</script>";
        session_destroy(); 
        exit(); 
    }
    
    if($tipo=='1'){ $usuarios=$mysqli->query("SELECT id_usuario, tbl_TipoUsuario.nombre AS categoria, idTipo, usuarios.nombre as name, usuario, clave FROM usuarios JOIN tbl_TipoUsuario ON usuarios.idTipo=tbl_TipoUsuario.id_tpo"); }
    else{ $usuarios=$mysqli->query("SELECT id_usuario, tbl_TipoUsuario.nombre AS categoria, idTipo, usuarios.nombre as name, usuario, clave FROM usuarios JOIN tbl_TipoUsuario ON usuarios.idTipo=tbl_TipoUsuario.id_tpo WHERE tbl_TipoUsuario.id_tpo NOT IN (1,$acceso)"); }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <?php include('../base/head.php');?>
    </head>
    <body>
        <div class="row">
            <table>
            <tr>
                <th>Nombre</th>
                <th>Categoria</th>
                <th>Usuario</th>
                <th>Contraseña</th>
                <th></th>
                <th></th>
            </tr>
                <tr>
                    <form method="post">
                        <td><input name="name" type="text" placeholder="Nombre" value="" required></td>
                        <td><select name='categorias' required><option value="" selected>Selecciona Categoria</option>
                            <?php $categorias=$mysqli->query("SELECT * FROM `tbl_TipoUsuario` WHERE nombre <> 'Administrador'");
                            while($c=$categorias->fetch_assoc()){?>
                            <option value="<?php echo $c['id_tpo'];?>"><?php echo $c['nombre'];?></option>
                            <?php }?>
                            </select>
                        </td>
                        <td><input name="user" type="text" value="" placeholder="Usuario" required></td>
                        <td><input name="pass" type="text" value="" placeholder="Contraseña" required></td>
                        <td colspan="2" align="center"><input type="submit" class="button" name="nuevo_user" value="Crear Usuario"></td>
                    </form>
                </tr>
                <?php while($users=$usuarios->fetch_assoc()){ ?>    
                    <tr align="center">
                        <form method="POST" id="form1">
                            <td><input name="nombre" type="text" value="<?php echo $users['name'];?>" required>
                                <input name="ids" type="hidden" value="<?php echo $users['id_usuario'];?>"></td>
                            <td><select name='categoria_' required><option value="" selected>Selecciona Categoria</option>
                                <?php $categorias=$mysqli->query("SELECT * FROM `tbl_TipoUsuario` WHERE nombre <> 'Administrador'");
                                while($c=$categorias->fetch_assoc()){?>
                                <option value="<?php echo $c['id_tpo'];?>" <?php if($users['idTipo']==$c['id_tpo']){echo "selected";}?>><?php echo $c['nombre'];?></option>
                                <?php }?>
                                </select>
                            </td>
                            <td><input name="usuario" type="text" value="<?php echo $users['usuario'];?>" required></td>
                            <?php /*<td><input name="password" type="text" value="<?php echo $users['clave'];?>" required></td>*/?>
                            <td><a><button type="submit" title='Editar Datos!' name="editar" value="Editar" /><i class="fa fa-pencil" aria-hidden="true"></i></button></a></td>                            
                            <td><a><button type="submit" title='Eliminar Datos!' name="eliminar" value="Eliminar" /><i class="fa fa-times" aria-hidden="true"></i></button></a></td>
                            <td></td>
                        </form>
                    </tr>
                <?php }?>
            </table>
        </div>
        <?php
        if(isset($_POST['editar'])){
            $modificar=$mysqli->query("UPDATE `usuarios` SET `idTipo`='".$_POST['categoria_']."',`idCentral`='1',`nombre`='".$_POST['nombre']."',`usuario`='".$_POST['usuario']."' WHERE `id_usuario`='".$_POST['ids']."'");
            if($modificar){echo "<script>alert('Usuario modificado correctamente!');location.href='usuarios.php'</script>";}
        }
        if(isset($_POST['eliminar'])){ echo 'id:'.$_POST['ids'];
            $eliminar=$mysqli->query("DELETE FROM `usuarios` WHERE `id_usuario`='".$_POST['ids']."'");
            if($eliminar){echo "<script>alert('Usuario eliminado correctamente!');location.href='usuarios.php'</script>";}
        }
        if(isset($_POST['nuevo_user'])){ 
            $verificar=$mysqli->query("SELECT * FROM usuarios WHERE usuario='".$_POST['user']."'");
            if($verificar->num_rows<=0){
                $ingresar=$mysqli->query("INSERT INTO `usuarios` VALUES ('','".$_POST['categorias']."','1','".$_POST['name']."','".$_POST['user']."','".$_POST['pass']."')");
            }
            if($ingresar){echo "<script>alert('Usuario ingresado correctamente!');location.href='usuarios.php'</script>";}
        }
        ?>
    </body>
</html>