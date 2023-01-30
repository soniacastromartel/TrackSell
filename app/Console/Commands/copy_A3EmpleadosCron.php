<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\A3Empleado; 
use App\A3Centre;
use App\Employee; 
use App\EmployeeHistory;
use App\VistaEmpleados;
use DateTime;

class copy_A3EmpleadosCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'a3empleados:cron {dni?} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync user with A3 Database';

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
        \Log::channel('a3')->info("Importar datos de empleados A3 iniciado!");
        $dni = null !== ($this->argument('dni')) ? $this->argument('dni') : null; 
        $name = null !== ($this->argument('name')) ? $this->argument('name') : null; 
        try {
            /**  Busqueda en A3 de datos de empleados */
            $a3Empleado = new VistaEmpleados; 
            $a3Empleado->setConnection('a3');
            $query = $a3Empleado->select('NIF'
                                        , 'Nombre_Completo'
                                        , 'E-mail_personal as Email'
                                        , 'Telefono_1 as Telefono'
                                        , 'Codigo_Centro'
                                        , 'Codigo_Empleado' 
                                        , 'dbo.Vista_Empleados.Codigo_Empresa'
                                        , 'Categoria'
                                        , 'dbo.Vista_Empresa.Nombre_Empresa'
                                        , 'Fecha_de_alta_en_compañia'
                                        , 'Fecha_de_baja_en_compañia')
                                ->join('dbo.Vista_Empresa', 'dbo.Vista_Empresa.Codigo_Empresa', '=', 'dbo.Vista_Empleados.Codigo_Empresa')
                                ->join('dbo.Vista_Centros_De_Trabajo', 'dbo.Vista_Empresa.Codigo_Empresa', '=', 'dbo.Vista_Centros_De_Trabajo.Empresa_listada')
                                 ->whereNotIn('dbo.Vista_Centros_De_Trabajo.Empresa_listada', [10,11,14,15,20,23,24,25,69])
                          
                                //  ->where ([ ['dbo.Vista_Empleados.Codigo_Empresa', '!=', 1], ['dbo.Vista_Empleados.Codigo_Centro', '!=', 12]])
                                //  ->whereIn('dbo.Vista_Empresa.Codigo_Empresa', [1,2,3,4,5,6,7,8,9,12,13,17,19,21])

                                ; 

            //Parametro DNI - Sin parametros
            if (!empty($dni)) {
                $query = $query->where('NIF', $dni); 
            } else {
                $a3EmpleadoPDI = new A3Empleado; 
                $a3EmpleadoPDI->setConnection('');
                if (empty($name)) {
                    $a3EmpleadoPDI::truncate(); // Drops all rows from the table without logging individual row deletions
                }
            }
            $empleadosA3 = $query->get();

            $queryPDI = DB::table('employees');
            if (!empty($name)) {
                $queryPDI = $queryPDI->where('name', $name); 
            } else {
                $queryPDI = $queryPDI->whereNull('cancellation_date'); 
            }
            $empleadosPDI = $queryPDI
                            ->get();
            
            $updatedEmployeeIDS = [];
            $deletedEmployeeIDS = [];
            $contDeleted = 0;
            $contUpdated = 0; 
            $deleted = 0;
            
            /** Busqueda empleados A3 en PDI - Nombre o excepcion indicada en employee->nombre_a3 */
            foreach ($empleadosA3 as $ea3) {

                if (!empty($dni) && $deleted == 0  || !empty($name) && $deleted == 0  ) {
                    $a3empleado = A3Empleado::where('Nombre_Completo', $ea3->Nombre_Completo);
                    $a3empleado->delete();
                    $deleted = 1; 
                }

                $nombre = substr($ea3->Nombre_Completo, strpos($ea3->Nombre_Completo, ',')+2); 
                $nombre = $this->sanitize($nombre); 
    
                $apellidos = substr($ea3->Nombre_Completo,0, strpos($ea3->Nombre_Completo, ','));
                $apellidos = rtrim($apellidos);  //remove spaces at the end
                $apellidos = $this->sanitize($apellidos);
                
                $employeeFound = null; 
                foreach ($empleadosPDI as $epdi) {
                    //No realizar nada con employees validated. //FIXME... Solo carga inicial  
                    // if (empty($dni) && $epdi->validated == 1) continue;    esto no dejaba borrar algunos empleados
                    if (!empty($epdi->nombre_a3)) {    //EXCEPCIONES de importar automatico, casos indicados a mano
                        if ( $ea3->Nombre_Completo == $epdi->nombre_a3) {
                                $employeeFound = $epdi; 
                        }
                    } else {
                        $nameEmployee = $this->sanitize($epdi->name); 
                        $coincidenciaNombre = stripos( $nameEmployee, $nombre);  
                        if (strpos($nombre, ' ') !== false) {
                            $primer_nombre = substr($nombre, 0, strpos($nombre, ' ')); 
                            $segundo_nombre = substr($nombre, strpos($nombre, ' ')+1);
                            $coincidenciaPrimerNombre  = stripos( $nameEmployee, $primer_nombre); 
                            $coincidenciaSegundoNombre = stripos( $nameEmployee, $segundo_nombre); 
                        } else {
                            $coincidenciaPrimerNombre  = false; 
                            $coincidenciaSegundoNombre = false; 
                        }
                        if ( ( $coincidenciaNombre !== false || 
                        $coincidenciaPrimerNombre  !== false || 
                        $coincidenciaSegundoNombre !== false 
                        ) && (stripos( $nameEmployee, $apellidos) !== false)) {
                            $employeeFound = $epdi; 
                            break;         
                        }
                    }
                }
                /** Actualización de datos y employeeHistory */
                if (!empty($employeeFound)) {
                    A3Empleado::create($ea3->toArray());
                    if ($employeeFound->force_centre_id === 1) {
                        continue;  // No forzamos centro de empleado que lo tiene forzado
                    }
                    
                    $eToUpdate = Employee::find($employeeFound->id); 
                    $this->actualizarDatosEmpleado($eToUpdate, $ea3);
                    $centreId = $this->getCentrePdi($ea3);
                    if (!empty($ea3->Fecha_de_baja_en_compañia) && !in_array($employeeFound->id, $deletedEmployeeIDS)) {
                        $deletedEmployeeIDS[] = $employeeFound->id; 
                    } else if (empty($ea3->Fecha_de_baja_en_compañia)){
                        if (in_array($employeeFound->id, $deletedEmployeeIDS) ) {
                            $key = array_search($employeeFound->id, $deletedEmployeeIDS); 
                            unset($deletedEmployeeIDS[$key]); 
                        }
                         
                        if (empty($centreId)){
                            \Log::channel('a3')->info("Centro no encontrado en PDI, CodEmpresa:". $ea3->Codigo_Empresa .' CodCentro:' . $ea3->Codigo_Centro );
                            continue;
                        }
                        if (!in_array($employeeFound->id, $updatedEmployeeIDS)) {
                            $contUpdated += 1;
                            $updatedEmployeeIDS[] = $employeeFound->id; 
                        }
                        
                        
                    } 
                    \Log::channel('a3')->info("Actualizando datos de centro de empleado: " . $employeeFound->name);
                    $query = EmployeeHistory::where(['employee_id' => $employeeFound->id
                                                    ,'centre_id'   => $centreId ]); 
                    if (empty($ea3->Fecha_de_baja_en_compañia)) {
                        $employeeCentre =  $query->whereNull('cancellation_date')->first(); 
                    } else {
                        $employeeCentre =  $query->whereNotNull('cancellation_date')->first(); 
                    }
                    
                    if (empty($employeeCentre) ||  empty($employeeCentre->contract_startdate) || $employeeCentre->contract_startdate >$ea3->Fecha_de_alta_en_compañia ){
                        if (!empty($employeeCentre)) {
                            $employeeCentre->update(['contract_startdate'   => date("Y-m-d H:i:s", strtotime($ea3->Fecha_de_alta_en_compañia)) ]); 
                        } else {
                            if (!empty(($centreId)) && empty($ea3->Fecha_de_baja_en_compañia)) {
                                EmployeeHistory::create(['employee_id'          => $employeeFound->id
                                                        ,'rol_id'               => $employeeFound->rol_id
                                                        ,'centre_id'            => $centreId
                                                        ,'contract_startdate'   => $ea3->Fecha_de_alta_en_compañia
                                ]);
                            }
                        }
                    }
                }
            }
            
            /** Empleados a dar de baja */
            foreach ($deletedEmployeeIDS as $employeeID) {
                \Log::channel('a3')->info($employeeID);
                //Borramos Employee History ( aplicar cancellation_date) para marcarlo de baja
                $eToCancel = Employee::find($employeeID);
                // \Log::channel('a3')->info($employeeID);
                foreach ($empleadosA3 as $ea3) {
                    if ($ea3->NIF == $eToCancel->dni && !empty($ea3->Fecha_de_baja_en_compañia)) {
                        $centreId = $this->getCentrePdi($ea3); 
                        $ehToCancel = EmployeeHistory::where(['employee_id' => $employeeID,
                                                              'centre_id'   => $centreId
                                                        ])
                                                    ->whereNull('cancellation_date'); 
                        $ehToCancel->update(['cancellation_date'  => $ea3->Fecha_de_baja_en_compañia

                        ]);
                    }
                }

                $key = array_search($employeeID, $updatedEmployeeIDS); 
                if ($key !== false) {
                    \Log::channel('a3')->info("No se borra employeeID, para actualizar " . $employeeID); 
                     
                    continue; 
                }

                $dateCancel = null; 
                $ea3Cancel = null;
                foreach ($empleadosA3 as $ea3) {
                    if ($ea3->NIF == $eToCancel->dni) {
                        $centreId = $this->getCentrePdi($ea3); 
                        if (empty($centreId)){
                            \Log::channel('a3')->info("Centro no encontrado en PDI, CodEmpresa:". $ea3->Codigo_Empresa .' CodCentro:' . $ea3->Codigo_Centro );
                            \Log::channel('a3')->info("No se borra employeeID, no encontrado centro " . $employeeID); 
                            $ea3Cancel = $ea3;
                            continue;
                        } else {
                            //FIXME... quitarlo
                            //Se coge la ultima fecha de baja
                            if (empty($ea3Cancel) || $dateCancel < $ea3->Fecha_de_baja_en_compañia) {
                                $ea3Cancel = $ea3;
                                $dateCancel =  $ea3->Fecha_de_baja_en_compañia; 
                            }
                        }
                    }
                }
                $ea3 = $ea3Cancel; 
                $this->actualizarDatosEmpleado($eToCancel, $ea3);

                //si está de baja en todos los centros, se da de baja definitiva
                $contDeleted += 1; 
                \Log::channel('a3')->info("Dando de baja empleado en PDI con nombre: " . $eToCancel->name);

              
                $eToCancel->update(['baja_a3'             => true
                                    ,'cancellation_date'  => $ea3->Fecha_de_baja_en_compañia
                ]);
                $eHistory = EmployeeHistory::where(['employee_id' => $employeeID]); 

                //Actualizamos fecha de cancelacion que viene 
                $empleadoA3 = A3Empleado::where(['NIF' => $eToCancel->dni])->get(); 
                $cancelDate = null; 
                foreach ($empleadoA3 as $ea3) {
                    if (empty($cancelDate) || $ea3->Fecha_de_baja_en_compañia > $cancelDate) {
                        $cancelDate = $ea3->Fecha_de_baja_en_compañia; 
                    }
                }
                if (!empty($cancelDate)) {
                    $eHistory->update([ 'cancellation_date' => $cancelDate]);
                }
            }
            /** Asignar centro principal */
            $this->asignarCentroPrincipal($updatedEmployeeIDS); 
            //FIXME... quitarlo
            $this->asignarCentroPrincipal($deletedEmployeeIDS);

            \Log::channel('a3')->info("Se han actualizado ". $contUpdated . "empleados");
            \Log::channel('a3')->info("Se han dado de baja ". $contDeleted . "empleados");
            \Log::channel('a3')->info("Importar datos de empleados A3 finalizado!");
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::channel('a3')->info("Error conexion de base de datos");
            \Log::channel('a3')->info($e); 
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar empleados, contacte con el administrador');
        }
    }

    function sanitize($cadena) {
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );
    
        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena
        );
    
        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena
        );
    
        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena
        );
    
        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena
        );
    
        $cadena = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $cadena
        );
    
    
        return strtolower($cadena);
    }

    function getCentrePdi($ea3){

        $centreId = A3Centre::where(['code_business' => $ea3->Codigo_Empresa, 'code_centre' => $ea3->Codigo_Centro])
                ->first();
        

        return !empty($centreId) ? $centreId->centre_id : null; 
    }

    function asignarCentroPrincipal($updatedEmployeeIDS) {
        \Log::channel('a3')->info("Iniciando la asignación inicial a empleados multicentro");
        foreach ($updatedEmployeeIDS as $employeeID) {
            
            $eHistory = EmployeeHistory::where(['employee_id' => $employeeID])->get();

            $minDate = null; 
            $centre_id = null; 
            foreach ($eHistory as $eh) {
                if ( (empty($minDate) || $minDate > $eh->contract_startdate ) && empty($eh->cancellation_date) ) {
                    $minDate = $eh->contract_startdate;
                    $centre_id = $eh->centre_id;
                }
            }
            if (!empty($centre_id)) {
                $employee = Employee::where(['id' =>  $employeeID])->first();
                $centreA3 = A3Centre::where(['centre_id' => $centre_id])
                                    ->first();

                $codEmployee = A3Empleado::where(['Codigo_Empresa'             => $centreA3->code_business
                                                  ,'Fecha_de_alta_en_compañia' => $minDate 
                                                  ,'NIF'                       => $employee->dni 
                                                ])->first();
                if ($employee->force_centre_id === 1) {
                    continue;  // No forzamos centro de empleado que lo tiene forzado
                }    
                
                if (!empty($centreA3)) {
                    $employee->update(['cod_business' => $centreA3->code_business
                                       ,'centre_id'   => $centre_id
                    ]); 

                    if (!empty($codEmployee)) {
                        $employee->update(['cod_employee'=> $codEmployee->Codigo_Empleado ]); 
                    }

                    \Log::channel('a3')->info("Empleado Multicentro: ".$employee-> name); 
                    //\Log::channel('a3')->info( $employee->toArray()); 
                    
                } else {
                    \Log::channel('a3')->info("No se encuentra centro A3 para centro PDI con id:" . $centre_id);
                }
            }
        }
        \Log::channel('a3')->info("Finalizado la asignación inicial a empleados multicentro");
    }

    function actualizarDatosEmpleado($eToUpdate, $ea3){
        
        $datosEmployee = $eToUpdate->toArray(); 
        //Solo actualizamos NIF cuando no hay
        if ( !empty($ea3->NIF)) {
            if (empty($datosEmployee['dni'])) {
                $eToUpdate->update(['dni'    => $ea3->NIF]); 
            }
        }
        
        if ( !empty($ea3->Categoria)) {
            $eToUpdate->update(['category' => $ea3->Categoria]);
        }
        if ( !empty($ea3->Telefono)) {
            $eToUpdate->update(['phone'    => $ea3->Telefono]); 
        }
        if ( !empty($ea3->Email)) {
            if (empty($datosEmployee['email'])) {
                $eToUpdate->update(['email'    => $ea3->Email]);
            }
        }

        if ( !empty($ea3->Codigo_Empleado)) {
            $eToUpdate->update(['cod_employee'    => (string)$ea3->Codigo_Empleado]); 
        }
        if ( !empty($ea3->Codigo_Empresa)) {
            $eToUpdate->update(['cod_business'    => (string)$ea3->Codigo_Empresa]); 
        }

    }
}
