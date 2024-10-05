<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cargueinventario;

class CargueinventariosController extends Controller
{
    ///////////////////CONFIGURACIÓN BASE DE DATOS//////////////////////////////

    /**
     * Se crea y retorna un arreglo con todas las conexiones configuradas
     */
    private function obtenerConexiones() {

        return array(
            'mysql_pymes',
            // 'mysql_ssmedellin',
            // 'mysql_ssbogota'
        );

    }  

    ///////////////////FIN CONFIGURACIÓN BASE DE DATOS//////////////////////////////



    ///////////////////CONSULTAS//////////////////////////////  
    
    /**
     * Actualiza los costos de productos de NAN a 0
     */
    private function ajustarCostoInvenario( $conn ) {

        (new Cargueinventario)->setConnection($conn)->where('costoproducto', 'NAN')
        ->update(['costoproducto' => 0]);

    }

    ///////////////////FIN CONSULTAS//////////////////////////////



    ///////////////////LÓGICA DE NEGOCIO//////////////////////////////

    
    /**
     * Función que va realizar ajustes en la tabla Cargue inventarios
     */
    private function ajustarCostoInventarioPorEmpresa( $conn ){
        
        // Realiza update a los productos que se encuentre con costo NAN
        $fcv = $this->ajustarCostoInvenario( $conn );

    }

    ///////////////////FIN LÓGICA DE NEGOCIO//////////////////////////////



    //función principal que actuará como el handle
    public function main() {

        // Obtiene las conexiones configuradas para el multi tenant
        $multiTenantConnection = $this->obtenerConexiones();
    
        foreach( $multiTenantConnection as $conn ) {
            // Funcion que ajusta los registros de los inventarios con precios en NAN
            $this->ajustarCostoInventarioPorEmpresa( $conn );
        } 
    
    }

}
