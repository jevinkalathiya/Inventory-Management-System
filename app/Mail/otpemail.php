<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class otpemail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp, $email, $name, $fromName;
    
    public function __construct($otp, $email, $name){
    $this->otp = $otp;
    $this->email = $email;
    $this->name = $name;
    $this->fromName = "SmartStock - OTP | Madhav Solutions";
    }

    /**
     * Get the message envelope.
     */
    public function build(){
    return $this->to($this->email, $this->name)
                ->from(config('mail.from.address'), $this->fromName) // ✅ email from .env, name dynamic
                ->subject('SmartStock Login OTP – Expires in 5 Minutes')
                ->view('mail.otp')
                ->with([
                    'otp' => $this->otp,
                ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
