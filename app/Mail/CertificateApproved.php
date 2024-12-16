<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class CertificateApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $filepath;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $filepath)
    {
        $this->user = $user;
        $this->filepath = $filepath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject('Certificate Approved')
            ->view('mail.certificate_approved') // Update the view name
            ->attach($this->filepath, [
                'as' => 'Certificate.' . pathinfo($this->filepath, PATHINFO_EXTENSION),
                'mime' => mime_content_type($this->filepath),
            ]);
    }
}
