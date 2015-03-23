<?php

/**
 * Clase para enviar mensaje, usando dependency injection (dev example)
 * Para usarlo en cualquier controller solo es necesario usar get('mensaje')
 *
 * @author carlos
 */
namespace Reporte\ServicioBundle\Mensaje;

class Mensaje {
    
    public function enviar($body = "Cuerpo del mensaje") {
        return $body;
    }
}

?>
