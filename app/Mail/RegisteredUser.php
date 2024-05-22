<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Employee;

class RegisteredUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($employeeData)
    {
        $this -> employee = $employeeData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    // $imagePath = public_path('path/to/your/logo.jpg');

    $logoHeader = public_path('/assets/img/emailDesign.png');
    $logoIcot = public_path('/assets/img/LOGOICOT.png');
    $logoInstagram = public_path('/assets/img/instagram.png');
    $logoFacebook = public_path('/assets/img/facebook.png');
    $logoLinkedin = public_path('/assets/img/linkedin.png');
    $logoYoutube = public_path('/assets/img/youtube.png');

    return $this->from(env('MAIL_FROM'))
                    ->subject($this->employee['subject'])
                    ->view($this->employee['view'])
                    ->with([
                    'employeeData' => $this->employee,
                    'logoHeader' => $logoHeader,
                    'logoIcot' => $logoIcot,
                    'logoInstagram' => $logoInstagram,
                    'logoFacebook' => $logoFacebook,
                    'logoLinkedin' => $logoLinkedin,
                    'logoYoutube' => $logoYoutube,
                    
                ]);
    }
}
