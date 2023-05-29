<?php
    session_start();
    include_once "../elementos_menu.php";
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

    //Obtener métricas de sucursal 1 (Polanco)
    $mysqli = include_once "../conexion.php";

    //Ventas
    $consulta = $mysqli->query("SELECT sum(subtotal) as ventas FROM pedidos INNER JOIN detalle_pedidos on detalle_pedidos.id_pedido = pedidos.id_pedido WHERE pedidos.id_sucursal = 1 AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act')");
    if(!$consulta){
        echo $mysqli->error;
    }
    $consulta = mysqli_fetch_all($consulta,MYSQLI_ASSOC);
    $ventas_1 = $consulta[0]['ventas'];

    //Clientes
    $consulta_2 = $mysqli->query("SELECT count(DISTINCT(id_cliente)) as num_clientes from pedidos WHERE id_sucursal = 1 AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act')");
    if(!$consulta){
        echo $mysqli->$error;
    }
    $consulta_2 = mysqli_fetch_all($consulta_2, MYSQLI_ASSOC);
    $clientes_1 = $consulta_2[0]['num_clientes'];

    //Pedidos
    $consulta_3 = $mysqli->query("SELECT COUNT(id_pedido) as num_pedidos from pedidos WHERE id_sucursal = 1 AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act')");
    if(!$consulta_3){
        echo $mysqli->error;
    }
    $consulta_3 = mysqli_fetch_all($consulta_3, MYSQLI_ASSOC);
    $pedidos_1 = $consulta_3[0]['num_pedidos'];

    //Orden promedio
    $consulta_4 = $mysqli->query("SELECT AVG(subtotal) as orden_avg FROM (select detalle_pedidos.id_pedido, sum(subtotal) as subtotal from detalle_pedidos INNER JOIN pedidos on pedidos.id_pedido = detalle_pedidos.id_pedido WHERE pedidos.id_sucursal = 1 AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act') GROUP by id_pedido) as tabla_totales");
    if(!$consulta_4){
        echo $mysqli->error;
    }
    $consulta_4 = mysqli_fetch_all($consulta_4,MYSQLI_ASSOC);
    $orden_prom1 = $consulta_4[0]['orden_avg'];

    //Obtener métricas de sucursal 2 (Condesa)

    //Ventas
    $consulta = $mysqli->query("SELECT sum(subtotal) as ventas FROM pedidos INNER JOIN detalle_pedidos on detalle_pedidos.id_pedido = pedidos.id_pedido WHERE pedidos.id_sucursal = 2 AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act')");
    if(!$consulta){
        echo $mysqli->error;
    }
    $consulta = mysqli_fetch_all($consulta,MYSQLI_ASSOC);
    $ventas_2 = $consulta[0]['ventas'];

    //Clientes
    $consulta_2 = $mysqli->query("SELECT count(DISTINCT(id_cliente)) as num_clientes from pedidos WHERE id_sucursal = 2 AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act')");
    if(!$consulta){
        echo $mysqli->$error;
    }
    $consulta_2 = mysqli_fetch_all($consulta_2, MYSQLI_ASSOC);
    $clientes_2 = $consulta_2[0]['num_clientes'];

    //Pedidos
    $consulta_3 = $mysqli->query("SELECT COUNT(id_pedido) as num_pedidos from pedidos WHERE id_sucursal = 2 AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act')");
    if(!$consulta_3){
        echo $mysqli->error;
    }
    $consulta_3 = mysqli_fetch_all($consulta_3, MYSQLI_ASSOC);
    $pedidos_2 = $consulta_3[0]['num_pedidos'];

    //Orden promedio
    $consulta_4 = $mysqli->query("SELECT AVG(subtotal) as orden_avg FROM (select detalle_pedidos.id_pedido, sum(subtotal) as subtotal from detalle_pedidos INNER JOIN pedidos on pedidos.id_pedido = detalle_pedidos.id_pedido WHERE pedidos.id_sucursal = 2 AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act') GROUP by id_pedido) as tabla_totales");
    if(!$consulta_4){
        echo $mysqli->error;
    }
    $consulta_4 = mysqli_fetch_all($consulta_4,MYSQLI_ASSOC);
    $orden_prom2 = $consulta_4[0]['orden_avg'];

    //Datos para grafica ingresos (fecha vs ingresos) Polanco(1)
    $consulta_5 = $mysqli->query("SELECT DATE_FORMAT(fecha_hora, '%d-%m-%Y') as fecha_pedido, sum(subtotal) as total FROM pedidos INNER JOIN detalle_pedidos on detalle_pedidos.id_pedido = pedidos.id_pedido WHERE pedidos.id_sucursal = 1 AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act') GROUP BY fecha_pedido ORDER BY pedidos.fecha_hora");
    if(!$consulta_5){
        echo $mysqli->error;
    }

    //Datos para grafica ingresos (fecha vs ingresos) Condesa(2)
    $consulta_6 = $mysqli->query("SELECT DATE_FORMAT(fecha_hora, '%d-%m-%Y') as fecha_pedido, sum(subtotal) as total FROM pedidos INNER JOIN detalle_pedidos on detalle_pedidos.id_pedido = pedidos.id_pedido WHERE pedidos.id_sucursal = 2 AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act') GROUP BY fecha_pedido ORDER BY pedidos.fecha_hora");
    if(!$consulta_6){
        echo $mysqli->error;
    }
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
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
        <a href="https://lafacultad.online/logout.php"><i class='bx bx-exit'></i></a>
    </header>
    <section>
        <h1>Informe General</h1>
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
        <div class="ingresos-grafica">
            <h2>Comparación de ventas</h2>
            <div class="grafica">
                <canvas id="graficaVentas"></canvas>
            </div>
        </div>
        <h2>Polanco</h2>
        <div class="tarjetas-resumen">
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bx-dollar"></i></div>
                <div class="content">
                    <p class="t-title">Ventas</p>
                    <p class="t-value">$<?= number_format($ventas_1,2,".",","); ?></p>
                </div>
            </div>
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bxs-smile"></i></div>
                <div class="content">
                    <p class="t-title">Clientes</p>
                    <p class="t-value"><?= $clientes_1; ?></p>
                </div>
            </div>
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bx-fork"></i></div>
                <div class="content">
                    <p class="t-title">Pedidos</p>
                    <p class="t-value"><?= $pedidos_1; ?></p>
                </div>
            </div>
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bxs-purchase-tag-alt"></i></div>
                <div class="content">
                    <p class="t-title">Orden promedio</p>
                    <p class="t-value">$<?= number_format($orden_prom1,2,".",","); ?></p>
                </div>
            </div>
        </div>
        <h2>Condesa</h2>
        <div class="tarjetas-resumen">
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bx-dollar"></i></div>
                <div class="content">
                    <p class="t-title">Ventas</p>
                    <p class="t-value">$<?= number_format($ventas_2,2,".",","); ?></p>
                </div>
            </div>
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bxs-smile"></i></div>
                <div class="content">
                    <p class="t-title">Clientes</p>
                    <p class="t-value"><?= $clientes_2; ?></p>
                </div>
            </div>
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bx-fork"></i></div>
                <div class="content">
                    <p class="t-title">Pedidos</p>
                    <p class="t-value"><?= $pedidos_2; ?></p>
                </div>
            </div>
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bxs-purchase-tag-alt"></i></div>
                <div class="content">
                    <p class="t-title">Orden promedio</p>
                    <p class="t-value">$<?= number_format($orden_prom2,2,".",","); ?></p>
                </div>
            </div>
        </div>
    </section>
    <?php
        $datos_ventas_1 = array();
        $labels_ventas_1 = array();
        foreach($consulta_5 as $row){
            $datos_ventas_1[] = $row['total'];
            $labels_ventas_1[] = $row['fecha_pedido'];
        }

        $datos_ventas_2 = array();
        $labels_ventas_2 = array();
        foreach($consulta_6 as $row){
            $datos_ventas_2[] = $row['total'];
            $labels_ventas_2[] = $row['fecha_pedido'];
        }
    ?>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js"></script>
    <script type="text/javascript">
        //Datos
        const ingresos_1 = <?php echo json_encode($datos_ventas_1) ?>;
        const ingresos_2 = <?php echo json_encode($datos_ventas_2) ?>;

        var fechas_1 = <?php echo json_encode($labels_ventas_1) ?>;
        var fechas_2 = <?php echo json_encode($labels_ventas_2) ?>;

        var fechas_juntas = fechas_1.concat(fechas_2);

        var fechas = fechas_juntas.filter((item,pos)=> fechas_juntas.indexOf(item) === pos);
        
        var ventas_1 = [];
        var ventas_2 = [];

        //Agregar los datos de ventas de cada sucursal por fecha
        fechas.forEach((element, index) => {
            //Checar si hay datos para la fecha en los labels
            if(fechas_1.indexOf(element) == -1){
                ventas_1[index] = 0;
            } else {
                ventas_1[index] = ingresos_1[fechas_1.indexOf(element)];
            }
            if(fechas_2.indexOf(element) == -1){
                ventas_2[index] = 0;
            } else {
                ventas_2[index] = ingresos_2[fechas_2.indexOf(element)];
            }
        });

        const data = {
            labels: fechas,
            datasets: [
                {
                    label: "Polanco",
                    data: ventas_1,
                    backgroundColor: 'rgb(17, 62, 110)',
                },
                {
                    label: "Condesa",
                    data: ventas_2,
                    backgroundColor: 'rgb(30, 110, 195)',
                }
            ],
        };

        //Configuracion
        const config = {
            type: 'bar',
            data,
            options: {
                interaction: {
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
            }
        };

        //Renderizar
        new Chart(
            document.getElementById('graficaVentas'),
            config
        );
    </script>
</body>
</html>