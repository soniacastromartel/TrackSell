<?php

namespace App\Jobs;

use App\Centre;
use App\Target;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\UnupdatedVDNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckUnupdatedVDJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $developmentEmail;
    private $marketingEmail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->developmentEmail = env('MAIL_USERNAME');
        $this->marketingEmail = env('MARKETING_EMAIL');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $year = now()->year;
        $month = now()->month;

        $unupdatedTargets = Target::getUnupdatedVDTargets($year, $month);

        if ($unupdatedTargets->isNotEmpty()) {
            foreach ($unupdatedTargets as $target) {
                $email = Centre::getEmailByCenterId($target['centre_id']);
                if ($email) {
                    try {
                        Mail::to($this->developmentEmail)
                            ->cc($this->developmentEmail)
                            ->send(new UnupdatedVDNotification($target));
                        Log::info("Correo enviado a {$email} para el centro {$target['centre_id']}");
                    } catch (\Exception $e) {
                        Log::error("Error al enviar correo a {$email}: {$e->getMessage()}");
                    }
                } else {
                    Log::warning("El centro con ID {$target['centre_id']} no tiene email registrado.");
                }

            }
        }
    }
}
