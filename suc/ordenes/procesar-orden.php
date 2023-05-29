<?php

    session_start();

    if(isset($_GET['cliente']) && isset($_GET['mesa'])){
        //Obtener los datos conocidos: id_sucursal y id_empleado
        $sucursal = $_SESSION['suc_id'];
        $empleado = $_SESSION['id_user'];

        //Obtener cliente, mesa y fecha/hora de $_GET
        $cliente = $_GET['cliente'];
        $mesa = $_GET['mesa'];
        $fecha = $_GET['fecha'];

        //Obtener el id del cliente
        $mysqli = include_once "../../conexion.php";
        $consulta = $mysqli->query("SELECT * FROM clientes WHERE nombre = '$cliente'");
        if(!$consulta){
            echo $mysqli->error;
        }
        $consulta = mysqli_fetch_assoc($consulta);
        $id_cliente = $consulta['id_cliente'];
        
        //Crear registro en pedidos
        $consulta = $mysqli->query("INSERT INTO pedidos VALUES(NULL,'$id_cliente','$sucursal','$empleado','$mesa',0,'$fecha')");
        if(!$consulta){
            echo $mysqli->error;
        }

        //Obtener el id del pedido creado
        $consulta = $mysqli->query("SELECT id_pedido FROM pedidos ORDER BY id_pedido DESC LIMIT 1");
        if(!$consulta){
            echo $mysqli->error;
        }
        $consulta = mysqli_fetch_all($consulta,MYSQLI_ASSOC);
        $id_orden_last = $consulta[0]['id_pedido'];

        //Agregar registros de productos en detalle_pedidos
        $arr_orden = $_SESSION['carrito'];

        foreach($arr_orden as $p){
            $id_prod = intval($p['id']);
            $cant = intval($p['cantidad']);
            $subtotal = $cant * doubleval($p['precio']);
            $query = $mysqli->query("INSERT INTO detalle_pedidos VALUES (NULL,$id_orden_last,$id_prod,$cant,$subtotal)");
            if(!$query){
                echo $mysqli->error;
            }
        }

        //Vaciar productos de orden
        $_SESSION['carrito'] = array();
        
        header("Location: https://lafacultad.online/suc/");

    }

?>