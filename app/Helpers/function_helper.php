<?php
function fechaPerzo($fecha){
    $datos = explode('-', $fecha);
    $anio = $datos[0]; 
    $me = ltrim($datos[1], "0"); 
    $dia = $datos[2]; 
    $mes = array("","Enero",
                  "Febrero",
                  "Marzo",
                  "Abril",
                  "Mayo",
                  "Junio",
                  "Julio",
                  "Agosto",
                  "Septiembre",
                  "Octubre",
                  "Noviembre",
                  "Diciembre");
    return $dia." de ". $mes[$me] . " de " . $anio;
}

function verificar($valor, $datos = [])
{
    $existe = array_search($valor, $datos, true);
    return is_numeric($existe);
}

function get_nombre_dia($fecha)
{
    $fechats = strtotime($fecha); //pasamos a timestamp

    //lo devuelve en numero 0 domingo, 1 lunes,....
    switch (date('w', $fechats)) {
        case 0:
            return "Domingo";
            break;
        case 1:
            return "Lunes";
            break;
        case 2:
            return "Martes";
            break;
        case 3:
            return "Miercoles";
            break;
        case 4:
            return "Jueves";
            break;
        case 5:
            return "Viernes";
            break;
        case 6:
            return "Sabado";
            break;
    }
}