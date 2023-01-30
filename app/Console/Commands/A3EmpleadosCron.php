<?php

namespace App\Console\Commands;

use App\A3Centre;
use App\A3Employee;
use App\Employee;
use App\EmployeeHistory;
use App\Services\A3Service;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class A3EmpleadosCron extends Command
{
    protected $a3service;


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
    protected $description = 'Sync PDI Users with A3Laboral API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(A3Service $a3svc)
    {
        parent::__construct();

        $this->a3service = $a3svc;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::channel('a3')->info("Sincronizando datos de Empleados de PDI");
        $dni = null !== ($this->argument('dni')) ? $this->argument('dni') : null;
        $name = null !== ($this->argument('name')) ? $this->argument('name') : null;

        try {
            // $a3Employee= new A3Employee;
            $a3Employees = A3Employee::all();

            if (!empty($dni)) {
                $a3Employees = $a3Employees->where('identifierNumber', $dni);
            }


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


            foreach ($a3Employees as $ea3) {
                $nombre = substr($ea3->completeName, strpos($ea3->completeName, ',') + 2);
                $nombre = $this->a3service->sanitize($nombre);

                $apellidos = substr($ea3->completeName, 0, strpos($ea3->completeName, ','));
                $apellidos = rtrim($apellidos);  //remove spaces at the end
                $apellidos = $this->a3service->sanitize($apellidos);

                $employeeFound = null;

                foreach ($empleadosPDI as $epdi) {
                   
                    if ($ea3->completeName == $epdi->nombre_a3) {
                        $employeeFound = $epdi;
                    } else {
                        $nameEmployee = $this->a3service->sanitize($epdi->name);
                        $coincidenciaNombre = stripos($nameEmployee, $nombre);

                        if (strpos($nombre, ' ') !== false) {
                            $primer_nombre = substr($nombre, 0, strpos($nombre, ' '));
                            $segundo_nombre = substr($nombre, strpos($nombre, ' ') + 1);
                            $coincidenciaPrimerNombre  = stripos($nameEmployee, $primer_nombre);
                            $coincidenciaSegundoNombre = stripos($nameEmployee, $segundo_nombre);
                        } else {
                            $coincidenciaPrimerNombre  = false;
                            $coincidenciaSegundoNombre = false;
                        }
                        if (($coincidenciaNombre !== false ||
                            $coincidenciaPrimerNombre  !== false ||
                            $coincidenciaSegundoNombre !== false
                        ) && (stripos($nameEmployee, $apellidos) !== false)) {
                            if ($epdi->id == 1448){
                                $epdi->id ;
                            }
                            $employeeFound = $epdi;
                            break;
                        }
                    }
                }

                if (!empty($employeeFound)) {
                    if ($employeeFound->force_centre_id === 1 && $ea3->dropDate == null ) {
                         continue;  // No forzamos centro de empleado que lo tiene forzado
                    }
                    $eToUpdate = Employee::find($employeeFound->id);
                    $changes = $this->updatePDIEmployees($eToUpdate, $ea3);
                    if (!empty($changes)) {
                        $contUpdated += 1;
                        foreach (array_keys($changes) as $change) {
                            \Log::channel('a3')->info("Actualizando ' " . $change . "' para empleado " . $eToUpdate->name);
                        }
                    }
                    $centreId = $ea3->pdiCentre;
                    if ($ea3->dropDate != null && !in_array($employeeFound->id, $deletedEmployeeIDS)) {
                        $deletedEmployeeIDS[] = $employeeFound->id;
                    } else if ($ea3->dropDate == null) {
                        if (in_array($employeeFound->id, $deletedEmployeeIDS)) {
                            $key = array_search($employeeFound->id, $deletedEmployeeIDS);
                            unset($deletedEmployeeIDS[$key]);
                        }
                        if (empty($centreId)) {
                            \Log::channel('a3')->info("Centro no encontrado en PDI, CodEmpresa:" . $ea3->Codigo_Empresa . ' CodCentro:' . $ea3->Codigo_Centro);
                            continue;
                        }
                        if (!in_array($employeeFound->id, $updatedEmployeeIDS)) {
                            $updatedEmployeeIDS[] = $employeeFound->id;
                        }
                    }
                    \Log::channel('a3')->info("Actualizando datos de centro de empleado: " . $employeeFound->name);

                    $query = EmployeeHistory::where([
                        'employee_id' => $employeeFound->id, 'centre_id'   => $centreId
                    ]);
                    if ($ea3->dropDate == null) {
                        $employeeCentre =  $query->whereNull('cancellation_date')->first();
                    } else {
                        $employeeCentre =  $query->whereNotNull('cancellation_date')->first();
                    }

                    if (empty($employeeCentre) ||  empty($employeeCentre->contract_startdate) || $employeeCentre->contract_startdate > $ea3->enrolmentDate) {
                        if (!empty($employeeCentre)) {
                            $employeeCentre->update(['contract_startdate'   => date("Y-m-d H:i:s", strtotime($ea3->enrolmentDate))]);
                        } else {
                            if (!empty(($centreId)) && empty($ea3->dropDate)) {
                                EmployeeHistory::create([
                                    'employee_id'          => $employeeFound->id
                                    , 'rol_id'               => $employeeFound->rol_id
                                    , 'centre_id'            => $centreId
                                    , 'contract_startdate'   => $ea3->enrolmentDate
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
                foreach ($a3Employees as $ea3) {
                    if ($ea3->identifierNumber == $eToCancel->dni && $ea3->dropDate != null) {
                        $ehToCancel = EmployeeHistory::where([
                            'employee_id' => $employeeID,
                            'centre_id'   => $ea3->pdiCentre
                        ])
                            ->whereNull('cancellation_date');
                        $ehToCancel->update([
                            'cancellation_date'  => $ea3->dropDate

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
                foreach ($a3Employees as $ea3) {
                    if ($ea3->identifierNumber == $eToCancel->dni) {
                        $centreId = $ea3->pdiCentre;
                        if (empty($centreId)) {
                            \Log::channel('a3')->info("Centro no encontrado en PDI, CodEmpresa:" . $ea3->companyCode . ' CodCentro:' . $ea3->workplaceCode);
                            \Log::channel('a3')->info("No se borra employeeID, no encontrado centro " . $employeeID);
                            $ea3Cancel = $ea3;
                            continue;
                        } else {
                            //FIXME... quitarlo
                            //Se coge la ultima fecha de baja
                            if (empty($ea3Cancel) || $dateCancel < $ea3->dropDate) {
                                $ea3Cancel = $ea3;
                                $dateCancel =  $ea3->dropDate;
                            }
                        }
                    }
                }

                if ($ea3Cancel != null) {
                    $ea3 = $ea3Cancel;
                    $this->updatePDIEmployees($eToCancel, $ea3);

                    //si está de baja en todos los centros, se da de baja definitiva
                    $contDeleted += 1;
                    \Log::channel('a3')->info("Dando de baja empleado en PDI con nombre: " . $eToCancel->name);


                    $eToCancel->update([
                        'baja_a3'             => true, 'cancellation_date'  => $ea3->dropDate
                    ]);
                    $eHistory = EmployeeHistory::where(['employee_id' => $employeeID]);

                    //Actualizamos fecha de cancelacion que viene 
                    $empleadoA3 = A3Employee::where(['identifierNumber' => $eToCancel->dni])->get();
                    $cancelDate = null;
                    foreach ($empleadoA3 as $ea3) {
                        if (empty($cancelDate) || $ea3->dropDate > $cancelDate) {
                            $cancelDate = $ea3->dropDate;
                        }
                    }
                    if (!empty($cancelDate)) {
                        $eHistory->update(['cancellation_date' => $cancelDate]);
                    }
                } else {
                    continue;
                }
            }



            \Log::channel('a3')->info("Se han actualizado " . $contUpdated . " empleados");
            \Log::channel('a3')->info("Se han dado de baja " . $contDeleted .  " empleados");
            \Log::channel('a3')->info("¡Sincronizado de Empleados PDI Finalizado!");
        } catch (\Throwable $th) {
            \Log::channel('a3')->info("Error");
            \Log::channel('a3')->info($th);
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar empleados, contacte con el administrador');
        }
    }




    function updatePDIEmployees($eToUpdate, $ea3)
    {
        $datosEmployee = $eToUpdate->toArray();
        //Solo actualizamos NIF cuando no hay
        if (!empty($ea3->identifierNumber)) {
            if (empty($datosEmployee['dni'])) {
                $eToUpdate->update(['dni'    => $ea3->identifierNumber]);
            }
        }
        if (!empty($ea3->jobTitleDescription)) {
            $eToUpdate->update(['category' => $ea3->jobTitleDescription]);
        }
        if (!empty($ea3->personalphone)) {
            $eToUpdate->update(['phone'    => $ea3->personalphone]);
        }
        if (!empty($ea3->personalemail)) {
            if (empty($datosEmployee['email'])) {
                $eToUpdate->update(['email'    => $ea3->personalemail]);
            }
        }

        if (!empty($ea3->employeeCode)) {
            $eToUpdate->update(['cod_employee'    => (string)$ea3->employeeCode]);
        }
        if (!empty($ea3->companyCode)) {
            $eToUpdate->update(['cod_business'    => (string)$ea3->companyCode]);
        }

        if (!empty($ea3->completeName)) {
            $eToUpdate->update(['nombre_a3' => $ea3->completeName]);
        }

        return $eToUpdate->getChanges();
    }
}
