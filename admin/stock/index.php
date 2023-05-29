<?php
    session_start();
    include_once "../../elementos_menu.php";
    $puesto = $_SESSION['puesto'];
    $menu = elementosMenu($puesto);
    $usuario = $_SESSION['user'];

    $mysqli = include_once "../../conexion.php";

    //Obtener existencias de ambas sucursales
    $consulta = $mysqli->query("SELECT insumos.nombre, inventario.id_sucursal, SUM(inventario.cant) as existencia FROM inventario INNER JOIN insumos ON inventario.id_insumo = insumos.id_insumo GROUP BY insumos.nombre, inventario.id_sucursal");
    $datos_inv = mysqli_fetch_all($consulta,MYSQLI_ASSOC);
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
        <a href="https://lafacultad.online/logout.php"><i class='bx bx-exit'></i></a>
    </header>
    <section>
        <h1>Existencias del Inventario</h1>
        <div class="tabla-inv-admin">
            <div class="fila f-encabezados">
                <p>Producto</p>
                <p>Polanco</p>
                <p>Condesa</p>
            </div>
            <?php foreach($datos_inv as $x => $y){ 
                if(intval($y['id_sucursal']) == 2){
                    continue;
                } else {?>
                    <div class="fila f-datos">
                        <p><?= $y['nombre']; ?></p>
                        <p><?= $y['existencia']; ?></p>
                        <p><?= $datos_inv[$x+1]['existencia']; ?></p>
                    </div>
            <?php }
            } ?>
        </div>
    </section>
</body>
</html>