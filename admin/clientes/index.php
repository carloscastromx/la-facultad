<?php
    session_start();
    include_once "../../elementos_menu.php";
    $puesto = $_SESSION['puesto'];
    $menu = elementosMenu($puesto);
    $usuario = $_SESSION['user'];

    $fecha_1 = new DateTime("now", new DateTimeZone('America/Mexico_City'));
    $fecha_act = $fecha_1->format("Y-m-d");
    $fecha_1_output = $fecha_1->format("j/n/Y");

    if(isset($_GET['periodo'])){
        $periodo_val = intval($_GET['periodo']);

        $intervalo = "P".$_GET['periodo']."D";

        $fecha_2 = $fecha_1->sub(new DateInterval($intervalo));

    } else {
        $periodo_val = 30;
        $fecha_2 = $fecha_1->sub(new DateInterval('P30D'));
    }

    $fecha_old = $fecha_2->format("Y-m-d");
    $fecha_2_output = $fecha_2->format("j/n/Y");

    //Obtener datos de pedidos de cada cliente por sucursal
    $mysqli = include_once "../../conexion.php";

    $consulta = $mysqli->query("SELECT clientes.nombre as cliente, COUNT(pedidos.id_sucursal) as cant_pedidos, sucursales.nombre as sucursal from pedidos INNER JOIN clientes on clientes.id_cliente = pedidos.id_cliente INNER JOIN sucursales on sucursales.id_sucursal = pedidos.id_sucursal WHERE (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act') GROUP by clientes.nombre, pedidos.id_sucursal, sucursales.nombre ORDER BY clientes.nombre");
    $consulta = mysqli_fetch_all($consulta,MYSQLI_ASSOC);

    //Obtener num_pedidos por sucursal
    $consulta_2 = $mysqli->query("SELECT sucursales.nombre as sucursal, COUNT(pedidos.id_sucursal) as cant_pedidos from pedidos INNER JOIN clientes on clientes.id_cliente = pedidos.id_cliente INNER JOIN sucursales on sucursales.id_sucursal = pedidos.id_sucursal WHERE (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act') GROUP BY pedidos.id_sucursal, sucursales.nombre");
    $consulta_2 = mysqli_fetch_all($consulta_2,MYSQLI_ASSOC);
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
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
        <h1>Pedidos por sucursal</h1>
        <p class="dashboard-subtitle">Últimos <?php 
            if(isset($_GET['periodo'])){
                echo $_GET['periodo'];
            } else {
                echo "30";
            }
        ?> días (<?= $fecha_2_output . "  ->  " . $fecha_1_output; ?>)</p>
        <div class="row-control-periodo">
            <p>Periodo: </p>
            <form method="get">
                <select name="periodo" id="periodo-select">
                    <option value="7" <?php echo ($periodo_val == 7) ? "selected" : ""; ?>>7 días</option>
                    <option value="15" <?php echo ($periodo_val == 15) ? "selected" : ""; ?>>15 días</option>
                    <option value="30" <?php echo ($periodo_val == 30) ? "selected" : ""; ?>>30 días</option>
                    <option value="90" <?php echo ($periodo_val == 90) ? "selected" : ""; ?>>3 meses</option>
                    <option value="180" <?php echo ($periodo_val == 180) ? "selected" : ""; ?>>6 meses</option>
                    <option value="365" <?php echo ($periodo_val == 365) ? "selected" : ""; ?>>12 meses</option>
                </select>
                <input type="submit" value="Actualizar">
            </form>
        </div>
        <div class="tarjetas-resumen">
            <?php foreach($consulta_2 as $suc){ ?>
                <div class="tarjeta">
                    <div class="icon-box"><i class="bx bx-restaurant"></i></div>
                    <div class="content">
                        <p class="t-title"><?= $suc['sucursal']; ?></p>
                        <p class="t-value"><?= $suc['cant_pedidos']; ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <h1>Pedidos por cliente</h1>
        <div class="tabla-visitas-clientes">
            <div class="visitas-clientes-encabezados f-encabezados">
                <p>Cliente</p>
                <p># Pedidos</p>
                <p>Sucursal</p>
            </div>
            <?php foreach($consulta as $c) { ?>
                <div class="visitas-clientes-datos <?= $c['sucursal'];?>">
                    <p><?= $c['cliente']; ?></p>
                    <p><?= $c['cant_pedidos']; ?></p>
                    <p><?= $c['sucursal'];?></p>
                </div>
            <?php } ?>
        </div>
    </section>
</body>
</html>