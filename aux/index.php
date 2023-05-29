<?php
    session_start();
    include_once "../elementos_menu.php";
    $puesto = $_SESSION['puesto'];
    $menu = elementosMenu($puesto);
    $usuario = $_SESSION['user'];

    $id_sucursal = $_SESSION['suc_id'];

    $mysqli = include_once "../conexion.php";

    //Cant productos con bajo umbral de existencias (2)
    $consulta = $mysqli->query("SELECT COUNT(id_registro) as pocas_exist FROM inventario WHERE id_sucursal = $id_sucursal AND cant <= 2");
    if(mysqli_num_rows($consulta) == 0){
        $pocas_exist = 0;
    } else {
        $consulta = mysqli_fetch_all($consulta,MYSQLI_ASSOC);
        $pocas_exist = $consulta[0]['pocas_exist'];
    }

    //Tabla productos con bajo umbral de existencias
    $consulta_2 = $mysqli->query("SELECT nombre, cant, unidad FROM inventario INNER JOIN insumos on insumos.id_insumo = inventario.id_insumo WHERE id_sucursal = $id_sucursal AND cant <= 2 ORDER BY cant ASC");
    $tabla_umbral = mysqli_fetch_all($consulta_2,MYSQLI_ASSOC);

?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control: Auxiliar</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <nav>
        <div class="logo-nav">
            <img src="../img/logo.svg" alt="La Facultad" title="La Facultad">
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
        <div class="tarjetas-resumen">
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bxs-error"></i></div>
                <div class="content">
                    <p class="t-title">Baja existencia</p>
                    <p class="t-value"><?= $pocas_exist; ?></p>
                </div>
            </div>
        </div>
        <div class="tabla-pocas-exist <?php 
            if(intval($pocas_exist) == 0){
                echo "hide";
            } ?>">
            <div class="fila f-encabezados">
                <p>Producto</p>
                <p>Cantidad</p>
            </div>
            <?php foreach($tabla_umbral as $x){ ?>
                <div class="fila f-datos">
                    <p><?= $x['nombre']; ?></p>
                    <p><?= $x['cant']." ".$x['unidad']; ?></p>
                </div>
            <?php } ?>
        </div>
    </section>
</body>
</html>