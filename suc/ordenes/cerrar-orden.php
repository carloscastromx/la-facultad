<?php 

    session_start();

    $mysqli = include_once "../../conexion.php";

    if(!isset($_GET['id'])){
        header("Location: https://lafacultad.online/suc/");
    }

    $id_orden = $_GET['id'];

    //Actualizar el estatus de la orden a cerrado
    $consulta = $mysqli->query("UPDATE pedidos SET estatus = 1 WHERE id_pedido = $id_orden");
    if(!$consulta){
        echo $mysqli->error;
    }

    header("Location: https://lafacultad.online/suc/");

?>