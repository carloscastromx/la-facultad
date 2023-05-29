<?php
    session_start();
    include_once "../../elementos_menu.php";
    $puesto = $_SESSION['puesto'];
    $menu = elementosMenu($puesto);
    $usuario = $_SESSION['user'];

    //Fecha
    $fecha_hoy = new DateTime("now", new DateTimeZone('America/Mexico_City'));
    $fecha_pedido = $fecha_hoy->format("Y-m-d");
    
    $hora_pedido = intval($fecha_hoy->format("H"));

    $hora_real = $hora_pedido - 1;

    $hora_pedido = strval($hora_real).$fecha_hoy->format(":i");

    //Obtener usuarios para autocomplete
    $mysqli = include_once "../../conexion.php";
    $consulta = $mysqli->query("SELECT * from clientes");
    $arreglo_nombres = array();
    while($fila = mysqli_fetch_array($consulta)){
        $nombre = $fila["nombre"];
        array_push($arreglo_nombres,$nombre);
    }

    mysqli_free_result($consulta);

    //Obtener productos para mostrar
    $consulta = $mysqli->query("SELECT * FROM productos WHERE categoria = 'Alimentos'");
    $consulta_2 = $mysqli->query("SELECT * FROM productos WHERE categoria = 'Bebidas'");

    $total_orden = 0;

    if(isset($_POST['agregar_producto'])){
        if(isset($_SESSION['carrito'])){

            $ids_column = array_column($_SESSION['carrito'], 'id');

            if(!in_array($_GET['id'],$ids_column)){
                $arreglo_productos = array(
                    'id' => $_GET['id'],
                    'nombre' => $_POST['nom_prod'],
                    'imagen' => $_POST['img_prod'],
                    'precio' => doubleval($_POST['precio_prod']),
                    'cantidad' => intval($_POST['cant_prod'])
                );
    
                $_SESSION['carrito'][] = $arreglo_productos;
            } else{
                $array_key = array_search($_GET['id'], $ids_column);
                $cant_act = intval($_SESSION['carrito'][$array_key]['cantidad']);
                $cant_nuev = intval($_POST['cant_prod']) + $cant_act;

                $_SESSION['carrito'][$array_key]['cantidad'] = $cant_nuev;
            }

        } else{
            $arreglo_productos = array(
                'id' => $_GET['id'],
                'nombre' => $_POST['nom_prod'],
                'imagen' => $_POST['img_prod'],
                'precio' => doubleval($_POST['precio_prod']),
                'cantidad' => intval($_POST['cant_prod'])
            );

            $_SESSION['carrito'][] = $arreglo_productos;
        }
    }
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
        <h1>Crear Orden</h1>
        <div class="inputs-orden <?php 
            if(!isset($_SESSION['carrito']) || sizeof($_SESSION['carrito']) == 0){
                echo "hide";
            }
        ?>">
            <input type="text" name="nom_cliente" id="nom_cliente" placeholder="Nombre del cliente">
            <select name="num_mesa">
                <option value="1">Mesa 1</option>
                <option value="2">Mesa 2</option>
                <option value="3">Mesa 3</option>
                <option value="4">Mesa 4</option>
                <option value="5">Mesa 5</option>
                <option value="6">Mesa 6</option>
                <option value="7">Mesa 7</option>
                <option value="8">Mesa 8</option>
                <option value="9">Mesa 9</option>
                <option value="10">Mesa 10</option>
                <option value="11">Mesa 11</option>
            </select>
            <input type="date" name="fecha_orden" id="fecha_orden" value="<?= $fecha_pedido; ?>" disabled>
            <input type="time" name="hora_orden" id="hora_orden" value="<?= $hora_pedido; ?>" disabled>
        </div>
        <p class="validacion-nombre"></p>
        <h2 class="menu-h2 <?php 
            if(!isset($_SESSION['carrito']) || sizeof($_SESSION['carrito']) == 0){
                echo "hide";
            }
        ?>">Resumen de orden</h2>
        <div class="resumen-orden <?php 
            if(!isset($_SESSION['carrito']) || sizeof($_SESSION['carrito']) == 0){
                echo "hide";
            }
        ?>">
            <div class="encabezados-resumen">
                <p>Producto</p>
                <p>Precio</p>
                <p>Cantidad</p>
                <p>Subtotal</p>
            </div>
            <?php foreach($_SESSION['carrito'] as $x){ ?>
                <div class="resumen-fila">
                    <div class="img-producto">
                        <img src="../../img/<?= $x['imagen'];?>">
                        <p class="nom-producto"><?= $x['nombre']; ?></p>
                    </div>
                    <p class="precio-producto">$<?= number_format($x['precio'],2,".",","); ?></p>
                    <p class="cant-orden"><?= $x['cantidad']; ?></p>
                    <p class="subtotal">$<?= number_format($x['precio'] * $x['cantidad'],2,".",","); ?></p>
                    <?php $total_orden = $total_orden + ($x['precio'] * $x['cantidad']); ?>
                </div>
            <?php } ?>
            <div class="div-btn-ordenar">
                <div class="div-fin-orden">
                    <p class="total-orden">Total $<?= number_format($total_orden,2,".",","); ?></p>
                    <button type="submit" id="btn-ordenar" class="finalizar-orden">Crear orden</button>
                </div>
            </div>
        </div>
        <h2 class="menu-h2">Alimentos</h2>
        <div class="menu-alimentos">
            <?php while($p = mysqli_fetch_assoc($consulta)){ ?>
                <div class="row-producto">
                    <form method="post" action="index.php?id=<?= $p['id_producto']; ?>">
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
                    <input type="hidden" name="img_prod" value="<?= $p['imagen']; ?>">
                    <input type="submit" name="agregar_producto" value="Agregar">
                    </form>
                </div>
            <?php } mysqli_free_result($consulta); ?>
        </div>
        <h2 class="menu-h2">Bebidas</h2>
        <div class="menu-alimentos">
            <?php while($p = mysqli_fetch_assoc($consulta_2)){ ?>
                <div class="row-producto">
                    <form method="post" action="index.php?id=<?= $p['id_producto']; ?>">
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
                    <input type="hidden" name="img_prod" value="<?= $p['imagen']; ?>">
                    <input type="submit" name="agregar_producto" value="Agregar">
                    </form>
                </div>
            <?php } mysqli_free_result($consulta_2); ?>
        </div>
    </section>
    <script>
        $(document).ready(function(){
            $("#btn-ordenar").attr("disabled", "");

            var nombres = <?= json_encode($arreglo_nombres) ?>

            $("#nom_cliente").autocomplete({
                source: nombres
            });

            var nombre_orden = "";

            $("#nom_cliente").blur(function(){
                var valor = $("#nom_cliente").val();
                var match = $.inArray(valor, nombres);
                if(match == -1){
                    $("p.validacion-nombre").css("display","inline-block");
                    $("p.validacion-nombre").text("El cliente no existe, selecciona un nombre válido");
                    $("#btn-ordenar").attr("disabled", "");
                } else {
                    $("p.validacion-nombre").css("display","none");
                    $("p.validacion-nombre").text("");
                    $("#btn-ordenar").removeAttr("disabled");
                    nombre_orden = valor;
                }
            });

            $("#btn-ordenar").click(function(){
                var mesa = $('select').find(":selected").val();
                var fecha_pedido = document.querySelector("#fecha_orden").value + " " + document.querySelector("#hora_orden").value + ":00";
                if(!$("#btn-ordenar").is("[disabled]")){
                    window.location.href = "https://lafacultad.online/suc/ordenes/procesar-orden.php?cliente="+nombre_orden+"&mesa="+mesa+"&fecha="+fecha_pedido;
                }
            });
        });
    </script>
</body>
</html>