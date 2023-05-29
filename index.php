<?php

    session_start();

    if(isset($_SESSION['puesto'])){
        $puesto = $_SESSION['puesto'];
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
    }

?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="login">
    <?php if(isset($_GET['error'])){ ?>
    <div class="error-msg"><?php echo $_GET['error']; ?></div>
    <?php } ?>
    <div class="box">
        <div class="contenedor">
            <div class="top">
                <img src="img/logo.svg" alt="La Facultad" title="La Facultad">
                <h1>Iniciar Sesión</h1>
            </div>
            <form action="login.php" method="post"> 
                <div class="input-fila">
                    <input type="text" name="usuario" class="input" placeholder="Usuario" required>
                    <i class="bx bx-user"></i>
                </div>
                <div class="input-fila">
                    <input type="password" name="passwd" class="input" placeholder="Contraseña" required>
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-fila">
                    <input type="submit" class="btn-login" value="Ingresar">
                </div>
            </form> 
        </div>
    </div>
</body>
</html>