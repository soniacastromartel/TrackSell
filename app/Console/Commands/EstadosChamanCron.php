<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Actuacion; 
use App\ChamanSync;
use App\ChamanErrorSync;
use App\Tracking; 

class EstadosChamanCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'estadoschaman:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicio Cron Estados Chaman';

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
        \Log::debug("Inicio Cron Estados Chaman");
        //\Log::debug("Fin Cron Estados Chaman"); die();
        /**
         * SEGUIMIENTOS LAS PALMAS
         * 
         */
         \Log::channel('chaman')->info("Cron Estados Chaman Las Palmas iniciado!");
        $trackedGc = DB::table('export_target')
                                ->select( DB::raw("started_date,hc,service,tracking_id,GROUP_CONCAT(chaman_centres.code SEPARATOR ',') as centre_chaman"))
                                ->join('centres', 'export_target.tracking_centre_id', '=', 'centres.id')
                                ->join('chaman_centres', 'export_target.tracking_centre_id', '=', 'chaman_centres.centre_id')
                                ->whereNotNull('started_date')
                                ->whereNull('service_date') //Iniciada o Citada
                                ->whereIn("centres.island", ['GRAN CANARIA', 'FUERTEVENTURA', 'LANZAROTE']) 
                                ->groupBy('started_date', 'hc', 'service', 'tracking_id')
                                ->get();

        \Log::channel('chaman')->info('Seguimientos Iniciados / Citados en Las Palmas: ' . count($trackedGc)); 
        $actuacion = new Actuacion; 
        $actuacion->setConnection('chaman-gc');
        //$cont = 0;
        //$contNotFound = 0; 

        if (!empty($trackedGc)){
            $trackedGc = $trackedGc->toArray(); 
            $this->communicateChaman($trackedGc,$actuacion);
        }
        \Log::channel('chaman')->info("Cron Estados Chaman Las Palmas finalizado!");


        /**
         * SEGUIMIENTOS TENERIFE
         * 
         */
        \Log::channel('chaman')->info("Cron Estados Chaman Tenerife iniciado!");
        $trackedTf = DB::table('export_target')
        ->select( DB::raw("started_date,hc,service,tracking_id,GROUP_CONCAT(chaman_centres.code SEPARATOR ',') as centre_chaman"))
        ->join('centres', 'export_target.tracking_centre_id', '=', 'centres.id')
        ->join('chaman_centres', 'export_target.tracking_centre_id', '=', 'chaman_centres.centre_id')
        ->whereNotNull('started_date')
        ->whereNull('service_date') //Iniciada o Citada
        ->whereIn("centres.island", ['TENERIFE']) 
        ->groupBy('started_date', 'hc', 'service', 'tracking_id')
        ->get();

        \Log::channel('chaman')->info('Seguimientos Iniciados / Citados en Tenerife: ' . count($trackedTf)); 
        $actuacion = new Actuacion; 
        $actuacion->setConnection('chaman-tf');

        if (!empty($trackedTf)){
            $trackedTf = $trackedTf->toArray(); 
            $this->communicateChaman($trackedTf,$actuacion);
        }
        \Log::channel('chaman')->info("Cron Estados Chaman Tenerife finalizado!");

    }

    public function calculateLimitDate ($startedDate){

        $day = substr($startedDate,strrpos($startedDate, '-') +1);
        $month = substr($startedDate,strpos($startedDate, '-') +1, 2);                     
        $year = substr($startedDate,strpos($startedDate, '-') -4, 4); 
        $prevYear = $year; 
        $prevMonth = $month -1; 
        $nextYear = $year; 
        $nextMonth = $month +1;
        if (intval($month) == 1  ) {
            $prevMonth = "12";
            $prevYear -= 1; 
        }
        if (intval($month) == 12  ) {
            $nextMonth = "1";
            $nextYear += 1; 
        }

        if ($day > 20) {
            $minDate = $prevYear.'-'.str_pad($month, 2,"0", STR_PAD_LEFT) .'-21'; 
            $maxDate = $nextYear.'-'.str_pad($nextMonth, 2,"0", STR_PAD_LEFT) . '-20';  
        } else {
            $minDate = $prevYear.'-'.str_pad($prevMonth, 2,"0", STR_PAD_LEFT) .'-21'; 
            $maxDate = $nextYear.'-'.str_pad($month, 2,"0", STR_PAD_LEFT).'-20';
        }
        return ['minDate' => $minDate , 'maxDate' => $maxDate]; 
    }

    public function communicateChaman($trackData,$actuacion){

        $contNotFound = 0; 
        foreach ($trackData as $tracking) {
            if (!empty($tracking->hc)) {
                $hcSearch = str_pad($tracking->hc, 11,"0", STR_PAD_LEFT); 
                \Log::channel('chaman')->info("Buscando actuacion con HC: " . $hcSearch);  
                
                /** Buscamos en Chaman seguimientos del corte donde cuadra fecha de inicio */
                $limitDate = $this->calculateLimitDate($tracking->started_date); 
                $minDate = $limitDate['minDate']; 
                $maxDate = $limitDate['maxDate']; 
               

                $centresChaman = explode(",", $tracking->centre_chaman); 
                $actuaciones = $actuacion->select('SoliPres', 'FechEsta', 'EstaCita')
                                        ->whereRaw('NumePaci = ? AND FechEsta between ? and ?', [$hcSearch ,  date('Ymd',strtotime($minDate)), date('Ymd',strtotime($maxDate)) ])
                                        ->where('EstaCita', 'not like', "C%")
                                        ->where('TipoClae','=','P-Privado')
                                        ->where(function($q) use($centresChaman) {
                                            $q->where('SoliPres', 'like', $centresChaman[0]."%");
                                            if (count($centresChaman) > 1) {
                                                for($i= 1; $i<count($centresChaman); $i++){
                                                    $q->orWhere('SoliPres', 'like', $centresChaman[$i]."%"); 
                                                }
                                            }
                                        })
                                        ->get ();

                
                if ( empty($actuaciones->toArray())) {
                    \Log::channel('chaman')->info('No se tienen datos de actuaciones con el HC:' . $hcSearch);
                    //FIXME...
                    $error = new ChamanErrorSync; 
                    $error->hc = $hcSearch;
                    $error->started_date = $tracking->started_date;
                    $error->service = $tracking->service;
                    $error->error = 'Error, no se encuentra actuacion en Chaman para HC: ' . $hcSearch; 
                    $error->save();   
                        
                    $contNotFound +=1; 
                    continue; 
                }
                //\Log::channel('chaman')->info("Tratando actuaciones encontradas para el HC: " . $hcSearch);

                $isSyncronized = false; 
                foreach ($actuaciones->toArray() as $act) {


                    $actSyncronized = DB::table('chaman_sync')->where('SoliPres', '=', $act['SoliPres'])->first(); 
                    
                    /** 
                     * Preguntar si esa SoliPres ya está guardada, : 
                     *   si guardada: - evitar actualizar estado , paro programa  - escribo error en errores_chaman
                     *   si no está guardada - sigo 
                     * Guardar registro sincronizado con Chaman (json info en campo) - SoliPres
                    */
                    if (!empty($actSyncronized)) { // || $assignSeg == 1) {
                        
                        \Log::channel('chaman')->info("Actuacion con SoliPres:" . $act['SoliPres'] . " ya sincronizada");  
                        continue;      

                    } elseif (in_array($act['EstaCita'] , ['FI', 'PD', 'PI', 'RE', 'CI'])) {
                        \Log::channel('chaman')->info("Creando registro de Chaman_sync");  
                        $newActChaman = new ChamanSync;
                        $newActChaman->SoliPres = $act['SoliPres']; 
                        $newActChaman->response = trim(json_encode($act, true)); 
                        $newActChaman->pdi_request =  "HC " . $tracking->hc  . ', started_date ' . $tracking->started_date  . ', service ' .  $tracking->service;
                        
                        /**
                         * Actualizar estado seguimiento
                         */
                        $trackingDb = Tracking::find($tracking->tracking_id) ;

                        //Estado Citado,Realizado
                        if (in_array($act['EstaCita'] , ['FI', 'PD', 'PI', 'RE'])) {
                            \Log::channel('chaman')->info("Actualizando estado realizado, tracking con HC " . $tracking->hc  . ', started_date ' . $tracking->started_date  . ', service ' .  $tracking->service );  
                            $trackingDb->update([
                                'apointment_date'  => $act['FechEsta'] 
                                ,'apointment_done' => true 
                                ,'service_date'    => $act['FechEsta'] 
                                ,'service_done'    => true 
                            ]);
                            $newActChaman->State = 'Realizado';
                            \Log::channel('chaman')->info("Actualizado tracking_id: " . $tracking->tracking_id); 
                        }

                        //Estado Citado    
                        if ($act['EstaCita'] == 'CI') {
                            \Log::channel('chaman')->info("Actualizando estado citado,  tracking con HC " . $tracking->hc  . ', started_date ' . $tracking->started_date  . ', service ' .  $tracking->service );
                            $trackingDb->update([
                                'apointment_date'  => $act['FechEsta']
                                ,'apointment_done' => true
                            ]);
                            $newActChaman->State = 'Citado';
                            \Log::channel('chaman')->info("Actualizado tracking_id: " . $tracking->tracking_id);
                        }
                        $newActChaman->save(); 
                        $isSyncronized = true; 
                        break;  // Evitamos asociar de nuevo
                    }
                }
                if(!$isSyncronized) {
                    \Log::channel('chaman')->info("HC sin citar/realizar aún: " . $tracking->hc . ' con trackingId: ' . $tracking->tracking_id);
                }
            }
        }
        \Log::channel('chaman')->info("Numero de HC no encontrados: " . $contNotFound);
    }
}
