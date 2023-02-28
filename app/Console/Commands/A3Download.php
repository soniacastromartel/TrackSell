<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\A3Service;
use App\A3Centre;
use App\A3Employee;
use App\Centre;

class A3Download extends Command
{

    protected $a3service;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'a3:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download A3Laboral API Employees';

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

        \Log::channel('a3')->info("Iniciada sincronización de usuarios de PDI");
        $this->a3employeesDownload();
    }


    public function a3employeesDownload()
    {
        \Log::channel('a3')->info("Iniciada descarga de empleados desde A3Laboral");
        try {
            A3Employee::truncate();
            $centres = A3Centre::getA3Companies();
            $a3employee = new A3Employee;


            foreach ($centres as $companyCode) {
                $workplaces = A3Centre::getA3Centres($companyCode);
                foreach ($workplaces as $workplaceCode) {
                    try {
                        $totalPages= $this->a3service->getPages($companyCode, $workplaceCode);
                        \Log::channel('a3')->info('Páginas: ' . $totalPages);
                        for ($i = 0; $i <= $totalPages - 1; $i++) {
                            $pageNumber= $i+1;
                            $employees = $this->a3service->getEmployees($companyCode, $workplaceCode, $pageNumber);
                            if ($employees != null) {
                                foreach ($employees as $employee) {
                                    $employee = (object) [
                                        "employeeCode" => $employee['employeeCode'],
                                        "completeName" => $employee['completeName'],
                                        "identifierNumber" => $employee['identifierNumber'],
                                        "workplaceCode" => $employee['workplaceCode'],
    
                                    ];
                                    $a3employee = $employee;
                                    $job = $this->a3service->getJobTitle($companyCode, $employee->employeeCode);
                                    $personalData = $this->a3service->getContactData($companyCode, $employee->employeeCode);
                                    $hiringData = $this->a3service->getHiringData($companyCode, $employee->employeeCode);
    
                                    if (is_array($job)) {
                                        $a3employee->jobTitleDescription = $job['jobTitleDescription'];
                                    } else {
                                        $a3employee->jobTitleDescription = '';
                                    }
                                    if (is_array($hiringData)) {
                                        if (isset($hiringData['enrolmentDate'])) {
                                            !empty($hiringData['enrolmentDate']) ? $a3employee->enrolmentDate = substr($hiringData['enrolmentDate'], 0, strpos($hiringData['enrolmentDate'], 'T')) : $a3employee->enrolmentDate = null;
                                        } else {
                                            $a3employee->enrolmentDate = null;
                                        }
                                        if (isset($hiringData['dropDate'])) {
                                            !empty($hiringData['dropDate']) ? $a3employee->dropDate = substr($hiringData['dropDate'], 0, strpos($hiringData['dropDate'], 'T')) : $a3employee->dropDate = null;
                                        } else {
                                            $a3employee->dropDate = null;
                                        }
                                    } else {
                                        $a3employee->enrolmentDate = null;
                                        $a3employee->dropDate = null;
                                    }
                                    if (is_array($personalData)) {
                                        !empty($personalData['personalemail']) ? $a3employee->personalemail = $personalData['personalemail'] : $a3employee->personalemail = '';
                                        !empty($personalData['personalphone']) ? $a3employee->personalphone = $personalData['personalphone'] : $a3employee->personalphone = '';
                                    } else {
                                        $a3employee->personalemail = '';
                                        $a3employee->personalphone = '';
                                    }
    
                                    $a3employee->companyCode = $companyCode;
                                    $a3employee->pdiCentre = A3Centre::getPDICentre($companyCode, $workplaceCode);
                                    $a3employee->workplaceName = Centre::getCentreName($a3employee->pdiCentre);
    
                                    $a3employee = (array) $a3employee;
                                    A3Employee::create($a3employee);
                                    \Log::channel('a3')->info("Creado Empleado " . $a3employee['completeName']);
                                }
                            } else {
                                \Log::channel('a3')->info("No hay empleados para el centro " . $workplaceCode . " de la compañía " . $companyCode);
                                continue;
                            }

                        }
                       
                    } catch (\Throwable $e) {
                        \Log::channel('a3')->info("Error");
                        \Log::channel('a3')->info($workplaceCode);
                        \Log::channel('a3')->info($e);
                        continue;
                    }

                }
            }

            \Log::channel('a3')->info("Finalizada descarga de empleados desde A3Laboral");
        } catch (\Throwable $e) {
            \Log::channel('a3')->info("Error");
            \Log::channel('a3')->info($e);
        }
    }
}
