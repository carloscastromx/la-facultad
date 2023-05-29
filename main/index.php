<?php
    session_start();
    include_once "../elementos_menu.php";
    $puesto = $_SESSION['puesto'];
    $menu = elementosMenu($puesto);
    $usuario = $_SESSION['user'];

    $id_sucursal = $_SESSION['suc_id'];

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

    //Obtener las métricas
    $mysqli = include_once "../conexion.php";
    $empleado = $_SESSION['id_user'];

    //Ingresos
    $consulta = $mysqli->query("SELECT sum(subtotal) as ingresos FROM pedidos INNER JOIN detalle_pedidos on detalle_pedidos.id_pedido = pedidos.id_pedido WHERE pedidos.id_sucursal = $id_sucursal AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act')");
    if(!$consulta){
        echo $mysqli->error;
    }
    $consulta = mysqli_fetch_all($consulta,MYSQLI_ASSOC);
    $ingresos = $consulta[0]['ingresos'];

    //Clientes
    $consulta_2 = $mysqli->query("select count(DISTINCT(id_cliente)) as num_clientes from pedidos WHERE id_sucursal = $id_sucursal AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act')");
    if(!$consulta){
        echo $mysqli->$error;
    }
    $consulta_2 = mysqli_fetch_all($consulta_2, MYSQLI_ASSOC);
    $num_clientes = $consulta_2[0]['num_clientes'];

    //Pedidos
    $consulta_3 = $mysqli->query("select COUNT(id_pedido) as num_pedidos from pedidos WHERE id_sucursal = $id_sucursal AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act')");
    if(!$consulta_3){
        echo $mysqli->error;
    }
    $consulta_3 = mysqli_fetch_all($consulta_3, MYSQLI_ASSOC);
    $num_pedidos = $consulta_3[0]['num_pedidos'];

    //Orden promedio
    $consulta_4 = $mysqli->query("SELECT AVG(subtotal) as orden_avg FROM (select detalle_pedidos.id_pedido, sum(subtotal) as subtotal from detalle_pedidos INNER JOIN pedidos on pedidos.id_pedido = detalle_pedidos.id_pedido WHERE pedidos.id_sucursal = $id_sucursal AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act') GROUP by id_pedido) as tabla_totales");
    if(!$consulta_4){
        echo $mysqli->error;
    }
    $consulta_4 = mysqli_fetch_all($consulta_4,MYSQLI_ASSOC);
    $orden_avg = $consulta_4[0]['orden_avg'];

    //Datos para grafica ingresos (fecha vs ingresos)
    $consulta_5 = $mysqli->query("SELECT DATE_FORMAT(fecha_hora, '%d-%m-%Y') as fecha_pedido, sum(subtotal) as total FROM pedidos INNER JOIN detalle_pedidos on detalle_pedidos.id_pedido = pedidos.id_pedido WHERE pedidos.id_sucursal = $id_sucursal AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') >= '$fecha_old') AND (DATE_FORMAT(fecha_hora, '%Y-%m-%d') <='$fecha_act') GROUP BY fecha_pedido");
    if(!$consulta_5){
        echo $mysqli->error;
    }

?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control: Gerente</title>
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
        <h1>Resumen</h1>
        <p class="dashboard-subtitle">Últimos <?php 
            if(isset($_GET['periodo'])){
                echo $_GET['periodo'];
            } else {
                echo "30";
            }
        ?> días (<?= $fecha_2_output . "  ->  " . $fecha_1_output; ?>)</p>
        <div class="tarjetas-resumen">
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bx-dollar"></i></div>
                <div class="content">
                    <p class="t-title">Ingresos</p>
                    <p class="t-value">$<?= number_format($ingresos,2,".",","); ?></p>
                </div>
            </div>
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bxs-smile"></i></div>
                <div class="content">
                    <p class="t-title">Clientes</p>
                    <p class="t-value"><?= $num_clientes; ?></p>
                </div>
            </div>
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bx-fork"></i></div>
                <div class="content">
                    <p class="t-title">Pedidos</p>
                    <p class="t-value"><?= $num_pedidos; ?></p>
                </div>
            </div>
            <div class="tarjeta">
                <div class="icon-box"><i class="bx bxs-purchase-tag-alt"></i></div>
                <div class="content">
                    <p class="t-title">Orden promedio</p>
                    <p class="t-value">$<?= number_format($orden_avg,2,".",","); ?></p>
                </div>
            </div>
        </div>
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
            <h2>Evolución de los ingresos</h2>
            <div class="grafica">
                <canvas id="graficaIngresos"></canvas>
            </div>
        </div>
    </section>
    <?php
        $datos_ingresos = array();
        $labels_ingresos = array();
        foreach($consulta_5 as $row){
            $datos_ingresos[] = $row['total'];
            $labels_ingresos[] = $row['fecha_pedido'];
        }
    ?>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js"></script>
    <script type="text/javascript">
        //Datos
        const ingresos = <?php echo json_encode($datos_ingresos) ?>;
        const fechas = <?php echo json_encode($labels_ingresos) ?>

        const data = {
            labels: fechas,
            datasets: [{
                labels: "Ingresos",
                data: ingresos,
                borderColor: 'rgb(17, 62, 110)',
                borderWidth: 2,
                tension: 0.8,
                backgroundColor: 'rgba(30, 110, 195, 0.3)',
                pointRadius: 1.5,
                fill: {
                    target: 'origin'
                }
                }],
        };

        //Configuracion
        const config = {
            type: 'line',
            data,
            options: {
                plugins:{
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        //Renderizar
        new Chart(
            document.getElementById('graficaIngresos'),
            config
        );
    </script>
</body>
</html>