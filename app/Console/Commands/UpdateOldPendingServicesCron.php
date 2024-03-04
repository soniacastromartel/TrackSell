<?php

namespace App\Console\Commands;

use App\Tracking;
use Illuminate\Console\Command;

//!COMMAND CLASS USED FOR UPDATING PENDING TRAKINGS UPPER THAN 60 DAYS

class UpdateOldPendingServicesCron extends Command
{
    //TODO - DEFINE HOW TO INVOCATE THIS COMMAND
    protected $signature = 'services:update_old_pending_services';
    protected $description = 'Update pending services older than 60 days';

    //TODO - CONSTRUCTOR : INITIALIZE THE CLASS , CALL THE CONSTRUCTOR OF THE PARENT CLASS (COMMA)

    public function __construct()
    {
        parent::__construct();
    }

    //TODO - MAIN LOGICAL FUNCTION
    public function handle()
    {
        $dateLimit = now()->subDays(60)->toDateTimeString();
        $servicesToUpdate = Tracking::where('state', env('STATE_PENDING'))
            ->whereDate('started_date', '<', $dateLimit)
            ->get();

        foreach ($servicesToUpdate as $services) {
            $services->update([
                'cancellation_date' => now(),
                'state' => 'Cancelado',
                'observations' => 'Cancelado automático por antiguedad',
                'cancellation_reason' => 'Cancelación automática después de 60 días en estado pendiente',
            ]);
        }
        $this->info('Services updated  to cancelled: ' . $servicesToUpdate->count());
    }
}
