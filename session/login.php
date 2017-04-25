<?php
    include('../dist/connection/connBD.php');
    session_start();
    session_destroy();

            if(isset($_POST["login"])){ 
              $user=$_POST['user'];
              $pass=$_POST['password'];
              $result=$mysqli->query("SELECT * FROM usuarios WHERE usuario='$user' AND clave='$pass'");
              $exist=$result->num_rows;
              if($result->num_rows > 0){ 
                  $data = $result->fetch_assoc();
                      session_start();  
                      $_SESSION["username"] = $data['nombre'];
                      $_SESSION['access']=$data['idTipo'];
                      $_SESSION["ultimoAcceso"]= date("Y-m-d H:i:s"); 
                      header("Location: http://rd.enorchile.com/dist/index.php"); 
                      die();
              }
              else{ 
                echo "<script>alert('Usuario y/o clave incorrecta! Vuela a intentarlo.')
                function redireccionar(){window.location='../session/';}
                setTimeout('redireccionar()', 0);
                </script>"; 
              }
            }
          ?> 