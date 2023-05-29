<?php
    session_start();
    include_once "../../elementos_menu.php";
    $puesto = $_SESSION['puesto'];
    $menu = elementosMenu($puesto);
    $usuario = $_SESSION['user'];

    $id_sucursal = $_SESSION['suc_id'];

    $mysqli = include_once "../../conexion.php";
    //Obtener el inventario de la sucursal
    $consulta = $mysqli->query("SELECT id_registro, nombre, precio_unitario, cant, unidad FROM inventario INNER JOIN insumos on insumos.id_insumo = inventario.id_insumo WHERE id_sucursal = $id_sucursal ORDER BY nombre ASC");
    $tbl_inventario = mysqli_fetch_all($consulta,MYSQLI_ASSOC);
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
        <h1>Resumen de inventario</h1>
        <div class="tabla-inventario view">
            <div class="fila f-encabezados">
                <p>Producto</p>
                <p>Precio Unitario</p>
                <p>Cantidad</p>
            </div>
            <?php foreach($tbl_inventario as $t){ ?>
            <div class="fila f-datos">
                <p><?= $t['nombre']; ?></p>
                <p>$<?= number_format($t['precio_unitario'],2,".",","); ?></p>
                <p><?= $t['cant']." ".$t['unidad']; ?></p>
            </div>
            <?php } ?>
        </div>
    </section>
</body>
</html>