<?php
    session_start();
    $mysqli = include_once "conexion.php";
    $user = $_POST["usuario"];
    $pass = $_POST["passwd"];
    $consulta = $mysqli->query("SELECT * FROM empleados WHERE usuario = '$user' AND pass = '$pass'");
    if(mysqli_num_rows($consulta) == 1){
        $resultado = $consulta->fetch_all(MYSQLI_ASSOC);
        $_SESSION['user'] = $resultado[0]['nombre'];
        $_SESSION['id_user'] = $resultado[0]['id_empleado'];

        $suc_id = $resultado[0]['id_sucursal'];
        $_SESSION['suc_id'] = $suc_id;

        if(intval($suc_id) == 1){
            $_SESSION['suc'] = "Polanco";
        } elseif(intval($suc_id) == 2){
            $_SESSION['suc'] = "Condesa";
        }
        $puesto = $resultado[0]['puesto'];
        $_SESSION['puesto'] = $puesto;

        //Redirigir al usuario en base a su puesto
        if($puesto == "Gerente"){
            header("Location: https://lafacultad.online/main/");
        }
        if($puesto == "Auxiliar"){
            header("Location: https://lafacultad.online/aux/");
        }
        if($puesto == "Mesero"){
            header("Location: https://lafacultad.online/suc/");
        }
        if($puesto == "Administrador"){
            header("Location: https://lafacultad.online/admin/");
        }
    } else{
        header("Location: https://lafacultad.online/?error=Error al ingresar, revisa tus datos de acceso");
    }
?>