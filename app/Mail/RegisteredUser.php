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
        //
        $this -> employee = $employeeData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    
    return $this->from(env('MAIL_FROM'))
                    ->subject($this->employee['subject'])
                    ->view($this->employee['view'])
                    ->with([
                    'employeeData' => $this->employee
                ]);
    }
}
