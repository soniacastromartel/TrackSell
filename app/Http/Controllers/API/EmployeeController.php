<?php

namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Employee;
use App\Services\TargetService;
use App\Centre; 

//use Illuminate\Support\Facades\Log;
class EmployeeController extends BaseController
{
    /**
     * MIS DATOS
     */
    public function info($userName)
    {

        try{
            
            $whereFields = 'true'; 
            if (empty($userName)){
                return $this->sendError('Error: se necesita id de empleado');      
            }
            $whereFields .=  " and employees.username = '" . $userName . "'";
            $whereFields .=  " and roles.name = 'SUPERVISOR'";
            $employeeData = Employee::select(
                          'employees.name'
                        , 'employees.username'
                        , 'employees.dni'
                        , 'employees.phone'
                        , 'employees.mobile_phone'
                        , 'supervisor.name as supervisor'
                        , 'employees.category'
                        , 'employees.centre_id'
                        , 'employees.email'
                        )
                    ->distinct('employees.id' )
                    ->join('employee_history','employee_history.centre_id', '=', 'employees.centre_id')
                    ->join('roles','roles.id', '=', 'employee_history.rol_id')
                    ->join('employees as supervisor','supervisor.id', '=', 'employee_history.employee_id')
                    ->whereNull(['employees.cancellation_date' , 'employee_history.cancellation_date'])    
                    ->whereRaw($whereFields)->first();

            if (empty($employeeData)) {
                return $this->sendError('Empleado no encontrado', 500 ); 
            }     
            $centres = $employeeData->getPrescriptorCenter(); 
            $employeeData = $employeeData->toArray();
            $employeeData['centres'] = $centres; 

            $success['user'] = $employeeData;
            return $this->sendResponse($success, '');
            
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos', 500 ); 

        }
    }

    public function updateInfo(Request $request, $userName)
    {
        try{
            $params = $request->all(); 
            if (isset($params['name'])) {
                return $this->sendError('Error: no se puede modificar el nombre o id de usuario');      
            }
            if (empty($userName)) {
                return $this->sendError('Error: se necesita id de empleado');    
            }
            $employee = Employee::where('username', $userName); 

            foreach ($params as $key => $data) {
                if ($key == 'image'){
                    $safeName = $userName.'.'.'png';
                    \Illuminate\Support\Facades\Storage::put('public'. env('URI_AVATAR_EMPLOYEE') . $safeName, base64_decode($data));
                } else {
                    $updateParams[$key] = $data; 
                }
            }
            $employee->update($updateParams);
            return $this->sendResponse('Empleado actualizado correctamente', '');
        
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos', 500 ); 
        }
    }

    public function getRanking(Request $request) {
        try{
            $params = $request->all(); 
            $beginYear    = date('Y');
            $currentMonth = ltrim(date('m'),'0');
            $acumulative  = false; 
            if (isset($params['year'])) {
                $beginYear = $params['year'];
                if (isset($params['month'])) {
                    $currentMonth = ltrim($params['month'],'0'); // AÑO Y MES PASADOS
                } else {
                    $currentMonth = '1';  //TODO EL AÑO
                    $acumulative  =  true;
                }    
            } elseif (isset($params['month'])) {
                $currentMonth = ltrim($params['month'],'0'); // MES PASADO Y AÑO EN CURSO
            }
            $params['month']     =  $currentMonth; 
            $params['acumulative'] =   $acumulative; 
            $targetService = new TargetService();
            
            if (!empty($request->get('centre_id'))) {
                $centres = Centre::where('id',$request->get('centre_id'))->get(); 
            } else {
                $centres = Centre::getCentersWithoutHCT();
            }
            $params['centre'] =  $centres;
            if(!isset($params['year'])){
                $params['year'] = $beginYear;
            }
            $params['monthYear'] = $params['month'] .'/'.$params['year'];
            
            $trackingCentre = $targetService->getExportTarget($params, true);
            $rankingData = $targetService->getRanking($trackingCentre, $params['centre'], $params['month'],$params['year'],$params['acumulative']);
            $success['ranking'] = $rankingData;
            return $this->sendResponse($success, '');

        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sendError('Error de base de datos', 500 ); 
        }          
    }

    /**
     * Control de accesos usuarios [MAXIMO 3 INTENTOS SEGUIDOS]
     */
    public function controlUser(Request $request){
        $params = $request->all();
        
        if (isset($params['username'])) {
            $employeeUsername = $params['username'];
            
            $employee = Employee::where('username', $employeeUsername);
            $employeeArray = $employee->get()->toArray();
            if(!empty($employee)){
            $type = $params['type'];
            $access = $employeeArray[0]['count_access'];
            if($type == '1'){
                if ($access >= 3) {
                    return $this->sendResponse('block', 500);
                } else {
                    $access++;
                    Employee::updatingAccess($employeeArray[0]['id'], $access);
                    return $this->sendResponse( $access, 'Access');
                }
            } else{
                return $this->sendResponse( $access, 'Access');
            }
            }
        } 
    } 
}