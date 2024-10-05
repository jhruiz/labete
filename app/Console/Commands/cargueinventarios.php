<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cargueinventario;

class cargueinventarios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ajustarcostocargueinventario';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ajusta los costos de los productos que se encuentran con valor NAN';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Obtiene las conexiones configuradas para el multi tenant
        $multiTenantConnection = $this->obtenerConexiones();
    
        foreach( $multiTenantConnection as $conn ) {
            // Funcion que ajusta los registros de los inventarios con precios en NAN
            $this->ajustarCostoInventarioPorEmpresa( $conn );
        } 
    }


        ///////////////////CONFIGURACIÓN BASE DE DATOS//////////////////////////////

    /**
     * Se crea y retorna un arreglo con todas las conexiones configuradas
     */
    private function obtenerConexiones() {

        return array(
            'mysql_advstore',
            'mysql_eliteautos',
            'mysql_motorlab',
            'mysql_motormedics',
            'mysql_pymes',
            'mysql_ssbogota',
            'mysql_ssmedellin',
            'mysql_servitecala10',
            'mysql_torque'
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
}
