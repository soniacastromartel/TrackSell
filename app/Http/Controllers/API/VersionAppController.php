<?php

namespace App\Http\Controllers\API;

use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

    class VersionAppController extends BaseController {

        public function checkingVersion(Request $request) {
            $versionLast = env('VERSION_APP');
            if (isset($request['version'])) {
                $appVersion = $request['version']; 
                if($versionLast === $appVersion){
                    return $this->sendResponse(env('RESPONSE_OK'), '');
                } else{
                    return $this->sendResponse(env('RESPONSE_UPDATE'),'');
                }
            } else{
                return $this->sendResponse(env('RESPONSE_NO_VALID'), '');
            }
        }

        /**
         * Display a listing of the resource.
         *
         * @param $platform = ['android', 'ios']
         * @param $type = ['l', 'a'] -> (l: link/ a: archive)
         * @return \Illuminate\Http\Response
        */
        public function getLastVersion(Request $request)
        {

            $platform = $request['platform'];
            $type = $request['type'];
            try{
                $latestVersion = env('VERSION_APP'); 
                $sourceLink = ''; 
                $nameFile = '';
                if ($platform == 'android') {
                    $nameFile = 'icot-app-'.$latestVersion.'.zip';
                    $sourceLink = 'public/versiones/' . $nameFile  ;  
                }
                if ($platform == 'ios') {
                    // $nameFile = 'icot-pwa-'.$latestVersion.'.html';
                    $nameFile = 'storage/versiones/ios/latest';
                    if ($type == 'a'){
                        return \Redirect::to($nameFile); 
                    }                    
                }
                if ($type == 'l'){
                    return $this->sendResponse([], $nameFile);
                } else{
                    ob_end_clean(); //NOT REMOVE.... whitespace remove before response
                    $headers = array('Content-Type: application/zip'); 
                    return \Illuminate\Support\Facades\Storage::download($sourceLink, $nameFile, $headers);
                }                
            } catch (\Illuminate\Database\QueryException $e) {
                return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar centros, contacte con el administrador');
            } 
        }

        /**
         * Get last changes of specific version
         *
         * @return \Illuminate\Http\Response
        */
        public function getLastChanges(Request $request)
        {
            $appVersion = $request['version'];
            chdir(env('DIR_GIT_VERSIONES'));
            exec('touch  git_log.txt   && chown www-data:www-data git_log.txt && chmod 777  git_log.txt 2>&1');
            $lastChanges = exec('git log origin/master --oneline > git_log.txt');
            $content = fopen(\Storage::path("public/versiones/git_log.txt"),'r');
            $versionChanges = '';
                while (!feof($content)) {
                    $line = fgets($content);
                    $versionChanges = strpos($line, $appVersion); 
                    if ($versionChanges !== false) {
                        $versionChanges = substr($line,8); 
                        break; 
                    }
                }
            $success = ['changes' => $versionChanges]; 
            return $this->sendResponse($success, '');
        }   
        
        /**
         * Comprueba la omision de actualizacion de la app
         * de manera continuada (max 3 intentos)
         * 
         * @param $userName - Nombre de usuario de consulta
         * @param $isSum - Suma el conteo de omision de actulizacion
         */
        public function notUpdate(Request $request) 
        {
            $userName = $request['data'];
            $isSum = $request['isSum'];
            if (!empty($userName)) {
                $employee = Employee::whereRaw("BINARY username = ?", [$userName])->first(); 
                $countUpdate = $employee->updateRequest;
                if ($isSum) {
                    if (!empty($employee)) {
                        $employee->update(['updateRequest' => ($countUpdate)+1]);
                        return $this->sendResponse($countUpdate, 200);
                    }
                } else {
                    return $this->sendResponse($countUpdate, 200);
                }
            } 
        }

        /**
         * Se resetea el contador de las solicitudes de actualizacion de app
         */
        public function resetCountUpdate(Request $request) 
        {
            $user = $request['user'];
            if (!empty($user)) {
                $employee = Employee::whereRaw("BINARY username = ?", [$user])->first();
                if (!empty($employee)) {
                    $employee->update(['updateRequest' => 0]);
                    return $this->sendResponse('', 200);
                }
            }
        }
    }