<?php
    session_start();
    include_once "../../elementos_menu.php";
    $puesto = $_SESSION['puesto'];
    $menu = elementosMenu($puesto);
    $usuario = $_SESSION['user'];

    $id_sucursal = $_SESSION['suc_id'];

    if(!isset($_GET['id']) || isset($_GET['id-act'])){
        header("Location: https://lafacultad.online/aux/stock/");
    }

    $mysqli = include_once "../../conexion.php";

    $id_prod = $_GET['id'];

    //Si esta el parametro para actualizar, ejecutar update
    if(isset($_GET['id-act']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
        $id_insumo = $_GET['id-act'];
        $nueva_cant = $_POST['cant_new'];

        $sql = $mysqli->query("UPDATE inventario SET cant = $nueva_cant WHERE id_insumo = $id_insumo and id_sucursal = $id_sucursal");

        if(!$sql){
            echo $mysqli->error;
        }

        header("Location: https://lafacultad.online/aux/stock/actualizar.php?id=$id_insumo&msg=Insumo actualizado correctamente");

    }

    //Obtener datos del producto
    $consulta = $mysqli->query("SELECT inventario.id_insumo, nombre, precio_unitario, cant, unidad FROM insumos INNER JOIN inventario on inventario.id_insumo = insumos.id_insumo WHERE id_registro = $id_prod");
    if(!$consulta){
        echo $mysqli->error;
    }
    $datos_insumo = mysqli_fetch_all($consulta,MYSQLI_ASSOC);
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/estilos.css">
</head>
<body>
    <nav>
        <div class="logo-nav">
            <img src="../../img/logo.svg" alt="La Facultad" title="La Facultad">
        </div>
        <div class="nav-text">
            <p>Secciones</p>
        </div>
        <div class="menu-nav">
            <div class="menu">
                <ul class="menu-links">
                    <?php foreach($menu as $el){?>
                        <li class="menu-link <?php echo elementoActivo($el['link']);?>">
                            <a href="<?php echo "https://lafacultad.online".$el['link']; ?>">
                                <i class="bx bxs-<?php echo $el['icon']; ?>"></i>
                                <span><?php echo $el['text']; ?></span>
                            </a>
                        </li>
                    <?php }?>
                </ul>
            </div>
        </div>
    </nav>
    <header>
        <span class="nombre-usuario"><?php echo $usuario; ?></span>
        <span class="puesto"><?= $puesto; ?></span>
        <span class="sucursal"><?= $_SESSION['suc']; ?></span>
        <a href="https://lafacultad.online/logout.php"><i class='bx bx-exit'></i></a>
    </header>
    <section>
        <?php if(isset($_GET['msg'])){ ?>
        <div class="msg-box"><?php echo $_GET['msg']; ?></div>
        <?php } ?>
        <h1 class="actualizar-h1"><?= $datos_insumo[0]['nombre']; ?></h1>
        <p class="actualizar-id">ID: <?= $datos_insumo[0]['id_insumo']; ?></p>
        <p class="actualizar-actual">Cantidad: <?= $datos_insumo[0]['cant']." ".$datos_insumo[0]['unidad']; ?></p>
        <p class="actualizar-precio">Precio Unitario: $<?= number_format($datos_insumo[0]['precio_unitario'],2,".",","); ?></p>
        <form class="actualizar-form" method="post" action="actualizar.php?id-act=<?= $id_prod; ?>">
            <div class="input-wrap">
                <label>En Existencia</label>
                <input type="text" name="cant_old" id="cant_old" value="<?= $datos_insumo[0]['cant']; ?>" disabled required readonly>
            </div>
            <div class="input-wrap">
                <label>Nueva cantidad</label>
                <input type="text" name="cant_new" id="cant_new" required>
            </div>
            <input type="submit" value="Actualizar">
        </form>
    </section>
</body>
</html>