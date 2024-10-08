<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacturaCuentaValore;

class FacturaCuentaValoresController extends Controller
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
     * Obtiene el total de las facturas cuentas valores de la última hora
     */
    private function obtenerFacturasCuentasValores( $conn ){

        return (new FacturaCuentaValore)->setConnection($conn)
        ->where('created', '>', now()->startOfDay())
        ->get();

    }

    /**
     * Actualiza el registro con el id de la factura calculado
     */
    private function arreglarRegistroFactura( $conn, $id, $idFactura ) {

        (new FacturaCuentaValore)->setConnection($conn)->where('id', $id)
        ->update(['factura_id' => $idFactura]);

    }

    ///////////////////FIN CONSULTAS//////////////////////////////





    ///////////////////LÓGICA DE NEGOCIO//////////////////////////////

    /**
     * Valida si existe una secuencia en la factura faltante
     */
    private function validarSecuencia( $prevFact, $nextFact ){

        $resp = false;

        // valida que los dos parámetros sean datos numéricos para validar la secuencia
        if( is_numeric($prevFact) && is_numeric($nextFact) ) {
            $resp = ($nextFact - $prevFact) == 2 ? true : false;
        }

        return $resp;
    }

    
    /**
     * Función que va realizar ajustes en la tabla de Facturas Cuentas Valores
     */
    private function ajustarFacturaCuentaValores( $conn ){
        
        //Se obtiene la información de las facturas cuentas valores de la última hora
        $fcv = $this->obtenerFacturasCuentasValores( $conn );

        foreach( $fcv as $key => $val ) {
            
            if( empty($val['factura_id']) ) {

                // Valida si la secuencia cumple con lo requerido para actualizar el id de la factura faltante
                if( $this->validarSecuencia( $fcv[$key - 1]['factura_id'], $fcv[$key + 1]['factura_id'] ) ) {

                    $idFactura = $fcv[$key - 1]['factura_id'] + 1;

                    // Ajusta el registro del id de factura faltante
                    $this->arreglarRegistroFactura( $conn, $val['id'], $idFactura );

                } 

            }
            
        }

    }

    ///////////////////FIN LÓGICA DE NEGOCIO//////////////////////////////






    //función principal que actuará como el handle
    public function main() {

        // Obtiene las conexiones configuradas para el multi tenant
        $multiTenantConnection = $this->obtenerConexiones();
  
        foreach( $multiTenantConnection as $conn ) {
          // Funcion que ajusta los registros de Factura Cuenta Valores
          $this->ajustarFacturaCuentaValores( $conn );
        } 
  
    }
}
