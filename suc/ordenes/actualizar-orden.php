<?php 

    session_start();
    include_once "../../elementos_menu.php";
    $puesto = $_SESSION['puesto'];
    $menu = elementosMenu($puesto);
    $usuario = $_SESSION['user'];

    if(!isset($_GET['id'])){
        header("Location: https://lafacultad.online/suc/");
    }

    $id_orden = $_GET['id'];

    $mysqli = include_once "../../conexion.php";

    //Obtener estatus de orden
    $consulta = $mysqli->query("SELECT estatus FROM pedidos WHERE id_pedido = $id_orden");
    if(!$consulta){
        echo $mysqli->error;
    }
    $consulta = mysqli_fetch_assoc($consulta);
    $status = intval($consulta['estatus']);

    if($status == 1){
        header("Location: https://lafacultad.online/suc/");
    }

    //Si esta el parametro para agregar producto
    if(isset($_GET['id']) && isset($_GET['add']) && isset($_POST['agregar_producto'])){
        $id_p = $_GET['add'];
        //Checar si el producto ya se habia pedido
        $consulta = $mysqli->query("SELECT cant, subtotal FROM detalle_pedidos WHERE id_pedido = $id_orden AND id_producto = $id_p");
        if(!$consulta){
            echo $mysqli->error;
        }
        //Si ya esta, actualizar cantidad y subtotal
        if(mysqli_num_rows($consulta) == 1){
            $datos = mysqli_fetch_assoc($consulta);
            mysqli_free_result($consulta);

            $cantidad_actual = intval($datos['cant']);
            $subtotal_actual = doubleval($datos['subtotal']);

            $cantidad_ordenada = intval($_POST['cant_prod']);
            $precio = doubleval($_POST['precio_prod']);

            $nueva_cantidad = $cantidad_actual + $cantidad_ordenada;
            $nuevo_subtotal = $precio * $nueva_cantidad;

            $consulta = $mysqli->query("UPDATE detalle_pedidos SET cant = $nueva_cantidad, subtotal = $nuevo_subtotal WHERE id_pedido = $id_orden AND id_producto = $id_p");
            if(!$consulta){
                echo $mysqli->error;
            }
        } else {
            //Si no esta, registrarlo en la tabla
            $precio_p = doubleval($_POST['precio_prod']);
            $cant_p = intval($_POST['cant_prod']);
            $subt_p = $precio_p * $cant_p;

            $consulta = $mysqli->query("INSERT INTO detalle_pedidos VALUES(NULL,$id_orden,$id_p,$cant_p,$subt_p)");
            if(!$consulta){
                echo $mysqli->error;
            }
        }

        //Redirigir a panel principal
        header("Location: https://lafacultad.online/suc/?msg=Orden actualizada correctamente");
    }

    //Obtener de detalle pedido los productos actuales registrados
    $consulta = $mysqli->query("SELECT pedidos.id_pedido, detalle_pedidos.id_producto, cant, subtotal, nombre, precio FROM pedidos INNER JOIN detalle_pedidos on detalle_pedidos.id_pedido = pedidos.id_pedido INNER JOIN productos on productos.id_producto = detalle_pedidos.id_producto WHERE pedidos.id_pedido = $id_orden");
    if(!$consulta){
        echo $mysqli->error;
    }
    $productos = mysqli_fetch_all($consulta,MYSQLI_ASSOC);
    mysqli_free_result($consulta);

    //Obtener alimentos y bebidas del menu para interfaz de agregar
    $consulta = $mysqli->query("SELECT * FROM productos WHERE categoria = 'Alimentos'");
    $alimentos = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
    mysqli_free_result($consulta);

    $consulta = $mysqli->query("SELECT * FROM productos WHERE categoria = 'Bebidas'");
    $bebidas = mysqli_fetch_all($consulta, MYSQLI_ASSOC);
    mysqli_free_result($consulta);

    $total_orden = 0;
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordenes</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/estilos.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
</head>
<body class="actualizar-orden">
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
        <h1>Actualizar orden #<?= $id_orden; ?></h1>
        <h2 class="menu-h2">Desglose de orden</h2>
        <div class="resumen-orden">
            <div class="encabezados-resumen">
                <p>Producto</p>
                <p>Precio</p>
                <p>Cantidad</p>
                <p>Subtotal</p>
            </div>
            <?php foreach($productos as $x){ ?>
                <div class="resumen-fila">
                    <p class="nom-producto"><?= $x['nombre']; ?></p>
                    <p class="precio-producto">$<?= number_format($x['precio'],2,".",","); ?></p>
                    <p class="cant-orden"><?= $x['cant']; ?></p>
                    <p class="subtotal">$<?= number_format($x['precio'] * $x['cant'],2,".",","); ?></p>
                    <?php $total_orden = $total_orden + ($x['precio'] * $x['cant']); ?>
                </div>
            <?php } ?>
            <div class="div-btn-ordenar">
                <div class="div-fin-orden">
                    <p class="total-orden">Total $<?= number_format($total_orden,2,".",","); ?></p>
                </div>
            </div>
        </div>
        <h2 class="menu-h2">Alimentos</h2>
        <div class="menu-alimentos">
            <?php foreach($alimentos as $p){ ?>
                <div class="row-producto">
                    <form method="post" action="actualizar-orden.php?id=<?= $id_orden; ?>&add=<?= $p['id_producto']; ?>">
                    <div class="img-producto">
                        <img src="../../img/<?= $p['imagen']; ?>">
                        <p class="nom-producto"><?= $p['nombre']; ?></p>
                    </div>
                    <p class="precio-producto">$<?= number_format($p['precio'],2,".",","); ?></p>
                    <div class="cant-producto">
                        <input type="number" placeholder="Cantidad" name="cant_prod" required>
                    </div>
                    <input type="hidden" name="nom_prod" value="<?= $p['nombre']; ?>">
                    <input type="hidden" name="precio_prod" value="<?= $p['precio']; ?>">
                    <input type="submit" name="agregar_producto" value="Agregar">
                    </form>
                </div>
            <?php }?>
        </div>
        <h2 class="menu-h2">Bebidas</h2>
        <div class="menu-alimentos">
        <?php foreach($bebidas as $p){ ?>
                <div class="row-producto">
                    <form method="post" action="actualizar-orden.php?id=<?= $id_orden; ?>&add=<?= $p['id_producto']; ?>">
                    <div class="img-producto">
                        <img src="../../img/<?= $p['imagen']; ?>">
                        <p class="nom-producto"><?= $p['nombre']; ?></p>
                    </div>
                    <p class="precio-producto">$<?= number_format($p['precio'],2,".",","); ?></p>
                    <div class="cant-producto">
                        <input type="number" placeholder="Cantidad" name="cant_prod" required>
                    </div>
                    <input type="hidden" name="nom_prod" value="<?= $p['nombre']; ?>">
                    <input type="hidden" name="precio_prod" value="<?= $p['precio']; ?>">
                    <input type="submit" name="agregar_producto" value="Agregar">
                    </form>
                </div>
            <?php }?>
        </div>
    </section>
</body>
</html>