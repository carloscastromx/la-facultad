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

        $dias = $_GET['periodo'];
        $intervalo = "P".$dias."D";

        $fecha_2 = $fecha_1->sub(new DateInterval($intervalo));

    } else {
        $periodo_val = 30;
        $fecha_2 = $fecha_1->sub(new DateInterval('P30D'));
    }

    $fecha_old = $fecha_2->format("Y-m-d");
    $fecha_2_output = $fecha_2->format("j/n/Y");

    $mysqli = include_once "../../conexion.php";

    //Obtener nombre del cliente, sucursal, mesero, fecha y total de los pedidos
    $consulta = $mysqli->query("SELECT clientes.nombre, sucursales.nombre as suc, empleados.nombre as emp, DATE_FORMAT(fecha_hora, '%d-%m-%Y') as fecha, SUM(subtotal) as total FROM pedidos INNER JOIN clientes on clientes.id_cliente = pedidos.id_cliente INNER JOIN empleados ON empleados.id_empleado = pedidos.id_empleado INNER JOIN sucursales on sucursales.id_sucursal = pedidos.id_sucursal INNER JOIN detalle_pedidos on detalle_pedidos.id_pedido = pedidos.id_pedido WHERE (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act') GROUP BY pedidos.id_pedido ORDER BY fecha_hora DESC");
    $filas = mysqli_num_rows($consulta);
    $consulta = mysqli_fetch_all($consulta,MYSQLI_ASSOC);
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordenes</title>
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
        <h1>Visión general de órdenes</h1>
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
        <div class="tabla-ordenes-admin">
            <div class="fila f-encabezados">
                <p>Cliente</p>
                <p>Sucursal</p>
                <p>Mesero</p>
                <p>Fecha</p>
                <p>Total</p>
            </div>
            <?php if($filas == 0){
                echo "<p style='text-align:center;margin-top:10px;'>No hay órdenes en el periodo especificado.</p>";
            } ?>
            <?php foreach($consulta as $o){ ?>
                <div class="fila f-datos <?= $o['suc']; ?>">
                    <p><?= $o['nombre'] ?></p>
                    <p><?= $o['suc']; ?></p>
                    <p><?= $o['emp']; ?></p>
                    <p><?= $o['fecha']; ?></p>
                    <p>$<?= number_format($o['total'],2,".",","); ?></p>
                </div>
            <?php } ?>
        </div>
    </section>
</body>
</html>