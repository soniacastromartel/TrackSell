<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UnupdatedVDNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $target;

    /**
     * Create a new message instance.
     *
     * @param \App\Target $target
     */
    public function __construct($target)
    {
        $this->target = $target;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $logoHeader = public_path('/assets/img/emailDesign.png');
        $logoIcot = public_path('/assets/img/LOGOICOT.png');
        $logoInstagram = public_path('/assets/img/instagram.png');
        $logoFacebook = public_path('/assets/img/facebook.png');
        $logoLinkedin = public_path('/assets/img/linkedin.png');
        $logoYoutube = public_path('/assets/img/youtube.png');
        $year = now()->year;
        $month = now()->month;

        return $this->from(env('MAIL_FROM'))
        ->subject('Venta Privada No Importada')
            ->view('emails.unupdated_vd')
            ->with([
                'year' => $year,
                'month' => $month,
                'centre_id' => $this->target->centre_id,
                'logoHeader' => $logoHeader,
                'logoIcot' => $logoIcot,
                'logoInstagram' => $logoInstagram,
                'logoFacebook' => $logoFacebook,
                'logoLinkedin' => $logoLinkedin,
                'logoYoutube' => $logoYoutube,
            ]);
    }
}
