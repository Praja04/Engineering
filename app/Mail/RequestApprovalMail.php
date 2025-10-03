<?php

namespace App\Mail;

use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sertifikat;
    public $approverName;

    /**
     * Create a new message instance.
     */
    public function __construct(KalibrasiSertifikatModel $sertifikat, $approverName)
    {
        $this->sertifikat = $sertifikat;
        $this->approverName = $approverName;
    }

    /**
     * Create a new message build.
     */
    public function build()
    {
        return $this->subject('Request Approval Sertifikat Kalibrasi')
            ->view('emails.approval.request') // ganti markdown jadi view
            ->with([
                'sertifikat'   => $this->sertifikat,
                'approverName' => $this->approverName,
            ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Request Approval Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.approval.request',
        );
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
