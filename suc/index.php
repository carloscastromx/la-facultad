<?php
    session_start();
    include_once "../elementos_menu.php";
    $puesto = $_SESSION['puesto'];
    $menu = elementosMenu($puesto);
    $usuario = $_SESSION['user'];

    //Fecha
    $fecha_hoy = new DateTime("now", new DateTimeZone('America/Mexico_City'));
    $fecha_hoy = $fecha_hoy->format("Y-m-d");

    //Obtener ordenes activas
    $id_mesero = $_SESSION['id_user'];

    $mysqli = include_once "../conexion.php";
    $consulta = $mysqli->query("SELECT id_pedido, nombre, mesa FROM pedidos INNER JOIN clientes ON pedidos.id_cliente = clientes.id_cliente WHERE pedidos.id_empleado = $id_mesero AND estatus = 0 ");
    if(!$consulta){
        echo $mysqli->error;
    }
    $filas = intval(mysqli_num_rows($consulta));
    $consulta = mysqli_fetch_all($consulta,MYSQLI_ASSOC);

    //Obtener ordenes cerradas de hoy
    $query = $mysqli->query("SELECT pedidos.id_pedido, mesa, clientes.nombre, SUM(subtotal) as total, DATE_FORMAT(fecha_hora, '%H:%i') as hora FROM detalle_pedidos INNER JOIN pedidos on pedidos.id_pedido = detalle_pedidos.id_pedido INNER JOIN clientes on clientes.id_cliente = pedidos.id_cliente WHERE pedidos.id_empleado = $id_mesero AND estatus = 1 AND DATEDIFF(fecha_hora,'$fecha_hoy') = 0 GROUP by pedidos.id_pedido, mesa, clientes.nombre, hora;");
    $resultados = mysqli_fetch_all($query, MYSQLI_ASSOC);

    $filas_2 = intval(mysqli_num_rows($query));
    mysqli_free_result($query);

?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control: Mesero</title>
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
    <?php if(isset($_GET['msg'])){ ?>
    <div class="msg-box"><?php echo $_GET['msg']; ?></div>
    <?php } ?>
        <h2>Ordenes actuales</h2>
        <?php 
            if($filas == 0){
                echo "<p class='no-orders'>No hay ordenes activas</p>";
            }
        ?>
        <div class="<?php 
            if($filas == 0){
                echo "hide";
            }
        ?> ordenes-actuales">
            <div class="fila-encabezados">
                <p>Cliente</p>
                <p>Mesa</p>
                <p></p>
                <p></p>
            </div>
            <?php foreach($consulta as $x){ ?>
                <div class="fila-datos">
                    <p><?= $x['nombre']; ?></p>
                    <p><?= $x['mesa']; ?></p>
                    <p><a href="ordenes/actualizar-orden.php?id=<?= $x['id_pedido']; ?>">Actualizar</a></p>
                    <p><a href="ordenes/cerrar-orden.php?id=<?= $x['id_pedido']; ?>">Cerrar</a></p>
                </div>
            <?php } ?>
        </div>
        <h2 style="margin-top: 15px;">Últimas ordenes cerradas</h2>
        <?php 
            if($filas_2 == 0){
                echo "<p class='no-orders'>No hay ordenes cerradas hoy</p>";
            }
        ?>
        <div class="<?php 
            if($filas_2 == 0){
                echo "hide";
            }
        ?> ordenes-cerradas">
            <div class="fila-encabezados">
                <p># Orden</p>
                <p>Mesa</p>
                <p>Cliente</p>
                <p>Hora</p>
                <p>Total</p>
            </div>
            <?php
            foreach($resultados as $x){ ?>
                <div class="fila-datos">
                    <p><?= $x['id_pedido']; ?></p>
                    <p><?= $x['mesa']; ?></p>
                    <p><?= $x['nombre']; ?></p>
                    <p><?= $x['hora'];?></p>
                    <p>$<?= number_format($x['total'],2,".",","); ?></p>
                </div>
            <?php } ?>
            
        </div>
    </section>
</body>
</html>