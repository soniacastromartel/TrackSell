<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CheckUnupdatedVDJob;

class CheckUnupdatedVDCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'targets:check-vd';

    protected $description = 'Verifica los Targets con campo VD no actualizado y envía notificaciones.';

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
        CheckUnupdatedVDJob::dispatch();
        $this->info('El Job para verificar Targets no actualizados ha sido ejecutado.');
    }
}
