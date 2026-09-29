<?php

/* * *****************************************************************************
 * get_date()
 *
 * Obtiene la fecha del sistema
 * ***************************************************************************** */

function get_date(): string
{
    //date('w') => w	Representación numérica del día de la semana 	0 (para domingo) hasta 6 (para sábado)
    //date('j') => j 	Día del mes sin ceros iniciales 	1 a 31
    //date('Y') => Y 	Una representación numérica completa de un año, 4 dígitos 	Ejemplos: 1999 o 2003
    //date("n") => n 	Representación numérica de una mes, sin ceros iniciales 	1 hasta 12
    $day = date('j');
    $year = date('Y');
    $strMouth = date("n");
    $strDay = date("w");

    switch (date("w")) {
        case 0:
            $strDay = "Domingo";
            break;
        case 1:
            $strDay = "Lunes";
            break;
        case 2:
            $strDay = "Martes";
            break;
        case 3:
            $strDay = "Mi&eacute;rcoles";
            break;
        case 4:
            $strDay = "Jueves";
            break;
        case 5:
            $strDay = "Viernes";
            break;
        case 6:
            $strDay = "S&aacute;bado";
            break;
    }

    switch (date("n")) {
        case 1:
            $strMouth = "Enero";
            break;
        case 2:
            $strMouth = "Febrero";
            break;
        case 3:
            $strMouth = "Marzo";
            break;
        case 4:
            $strMouth = "Abril";
            break;
        case 5:
            $strMouth = "Mayo";
            break;
        case 6:
            $strMouth = "Junio";
            break;
        case 7:
            $strMouth = "Julio";
            break;
        case 8:
            $strMouth = "Agosto";
            break;
        case 9:
            $strMouth = "Septiembre";
            break;
        case 10:
            $strMouth = "Octubre";
            break;
        case 11:
            $strMouth = "Noviembre";
            break;
        case 12:
            $strMouth = "Diciembre";
            break;
    }

    return $strDay . " , " . $day . " de " . $strMouth . " del " . $year;
}

// End get_date()
