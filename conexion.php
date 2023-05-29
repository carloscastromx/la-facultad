<?php
    $host = "localhost";
    $usuario_bd = "u637441115_usrcj94";
    $pass = "bE^J9!cw";
    $bd = "u637441115_lafacultad";

    $mysqli = new mysqli($host,$usuario_bd,$pass,$bd);
    if($mysqli->connect_errno){
        echo "Error en la conexión: Código (" . $mysqli->connect_errno . ") ". $mysqli->connect_error;
    }
    return $mysqli;
?>