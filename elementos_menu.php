<?php
    function elementosMenu($puesto){
        //icon,text,link
        if($puesto == "Gerente"){
            $elementos = array(
                array(
                    'icon'=>"home",
                    'text'=>"Panel de Control",
                    'link'=>"/main/"
                ),
                array(
                    'icon'=>"package",
                    'text'=>"Inventario",
                    'link'=>"/main/stock/"
                ),
                array(
                    'icon'=>"id-card",
                    'text'=>"Clientes",
                    'link'=>"/main/clientes/"
                ),
                array(
                    'icon'=>"receipt",
                    'text'=>"Ordenes",
                    'link'=>"/main/ordenes/"
                )
            );
        }
        if($puesto == "Administrador"){
            $elementos = array(
                array(
                    'icon'=>"home",
                    'text'=>"Panel de Control",
                    'link'=>"/admin/"
                ),
                array(
                    'icon'=>"package",
                    'text'=>"Inventario",
                    'link'=>"/admin/stock/"
                ),
                array(
                    'icon'=>"id-card",
                    'text'=>"Clientes",
                    'link'=>"/admin/clientes/"
                ),
                array(
                    'icon'=>"receipt",
                    'text'=>"Ordenes",
                    'link'=>"/admin/ordenes/"
                )
            );
        }
        if($puesto == "Auxiliar"){
            $elementos = array(
                array(
                    'icon'=>"home",
                    'text'=>"Panel de Control",
                    'link'=>"/aux/"
                ),
                array(
                    'icon'=>"package",
                    'text'=>"Inventario",
                    'link'=>"/aux/stock/"
                )
            );
        }
        if($puesto == "Mesero"){
            $elementos = array(
                array(
                    'icon'=>"home",
                    'text'=>"Panel de Control",
                    'link'=>"/suc/"
                ),
                array(
                    'icon'=>"receipt",
                    'text'=>"Ordenes",
                    'link'=>"/suc/ordenes/"
                )
            );
        }

        return $elementos;
    }
    
    function elementoActivo($url) {
        if ($_SERVER['REQUEST_URI'] == $url) {
          return 'active';
        } else {
            return '';
        }
      }
?>