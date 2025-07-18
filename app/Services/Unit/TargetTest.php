<?php

namespace App\Services\Unit;

use App\Centre;
use DB;
use App\Target;
use App\Services\TargetService;
use App\Tracking;
// use Tests\TestCase; 
use PHPUnit\Framework\TestCase;
use Log;

class TargetTest extends TestCase
{
    protected $center;
    protected $currentYear = 2022; 
    protected $tracking = [];

    
    public function getNextObjective ($month) {
        $targetService = new TargetService();
        $centerData = (object)$this->center->get()->toArray()[0]; 

        //TARGET
        $targetByCentre = $targetService->getTarget($this->center->get(), $month, $this->currentYear );

        if ($month == 1) {
            $this->assertSame(1, $targetByCentre[$centerData->id]->month );
        } else {
            $previousTarget = Target::where('month', $month-1)
                                    ->where('year', $this->currentYear)
                                    ->where('centre_id', $centerData->id)
                                    ->first(); 

            $nextTargetMonth = explode("/", $previousTarget->calc_month);
            $nextTargetMonth = intval($nextTargetMonth[0]);
            if ($previousTarget->obj1_done == 1 ) {
                $nextTargetMonth += 1 ; 
            } 
            $this->assertSame($nextTargetMonth, $targetByCentre[$centerData->id]->month );
        }
    }

    public function getStateTarget ($month,  $centre, $auxTargetDef) {
        $targetService = new TargetService();
        $stateTarget = $targetService->stateTarget($auxTargetDef , $month.'/'.$this->currentYear,$this->tracking, $centre);
        return $stateTarget; 
    }

    /**
     * Test funcionality TargetService.php -- getTarget 
     * 
     */
    public function test_tracing_get_target()
    {
        
        $centres = Centre::getActiveCentersWithoutDepartments(); 
        \Log::channel('testing')->debug('Testing TargetService->getTarget Year: ' . $this->currentYear); 
        \Log::channel('testing')->info('Iniciado Test'); 

        foreach ($centres as $centre) {
            $this->center = Centre::where('id', $centre->id);
            //if ( $centre->id != 20 ) continue; 
            for ($month = 1; $month<=12; $month++) {
               \Log::channel('testing')->debug('Testing TargetService->getTarget Centre: ' . $centre->name . ' Month:' . $month ); 
                $this->getNextObjective($month); 
            }
        }
    }


    /**
     * Test funcionality TargetService.php -- stateTarget 
     * 
     */
    public function test_tracing_state_target()
    {
        $centres = Centre::getActiveCentersWithoutDepartments(); 
        \Log::channel('testing')->debug('Testing TargetService->stateTarget Year: ' . $this->currentYear); 
        $targetService = new TargetService();
        foreach ($centres as $centre) {
            $this->center = Centre::where('id', $centre->id);   
            //$params['centre']    = $this->center->get();
            // if ($centre->id != 20) { 
            //     continue; 
            // } 
            
            $targetDef = $targetService->getMonthTarget($this->currentYear,$centre->id);
            for ($month = 1; $month<=12; $month++) {
               //$targetDef =  $targetService->getTarget($params['centre'],$month, $this->currentYear);
               \Log::channel('testing')->debug('Testing TargetService->stateTarget Centre: ' . $centre->name . ' Month:' . $month ); 
               $params['monthYear'] = $month . '/' . $this->currentYear; 
              
               $auxTracking = $targetService->getExportTarget($params);
               $this->tracking[$centre->name] = $auxTracking->toArray();
               //FIXME ERRORES AL CALCULAR ( CAMBIA OBJETIVOS, COMPARAR CON PROD)
               $stateTarget = $this->getStateTarget($month,$this->center , $targetDef);
               
               foreach($targetDef as $target) {
                    if ($target->month == $month )  {
                        $targetByCentre = $target;
                    }
               }
                
               $this->assertSame($targetByCentre->obj1_done, $stateTarget['vc']);
               $this->assertSame($targetByCentre->obj2_done, $stateTarget['vp']);
            }
        }
    }

}
