<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WaterQuotaAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $alertItems;
    public string $recipientName;
    public string $monthName;
    public int $year;
    public int $daysInMonth;

    /**
     * Create a new message instance.
     *
     * @param array $alertItems
     * @param string $recipientName
     * @param string $monthName
     * @param int $year
     * @param int $daysInMonth
     */
    public function __construct(
        array $alertItems,
        string $recipientName,
        string $monthName,
        int $year,
        int $daysInMonth
    ) {
        $this->alertItems = $alertItems;
        $this->recipientName = $recipientName;
        $this->monthName = $monthName;
        $this->year = $year;
        $this->daysInMonth = $daysInMonth;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $sourcesStr = implode(', ', array_column($this->alertItems, 'source'));
        return new Envelope(
            subject: "⚠️ Peringatan Kuota Air Mentah (≥ 80%): {$sourcesStr} [{$this->monthName} {$this->year}]",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.utility.water_quota_alert',
            with: [
                'alertItems'    => $this->alertItems,
                'recipientName' => $this->recipientName,
                'monthName'     => $this->monthName,
                'year'          => $this->year,
                'daysInMonth'   => $this->daysInMonth,
            ],
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
