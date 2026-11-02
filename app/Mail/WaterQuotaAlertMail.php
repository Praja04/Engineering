<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WaterQuotaAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $alertItems;
    public string $recipientName;
    public string $periodLabel;
    public string $startDate;
    public string $endDate;
    public int $daysInPeriod;

    /**
     * Create a new message instance.
     *
     * @param array $alertItems
     * @param string $recipientName
     * @param string $periodLabel
     * @param string $startDate
     * @param string $endDate
     * @param int $daysInPeriod
     */
    public function __construct(
        array $alertItems,
        string $recipientName,
        string $periodLabel,
        string $startDate,
        string $endDate,
        int $daysInPeriod
    ) {
        $this->alertItems    = $alertItems;
        $this->recipientName = $recipientName;
        $this->periodLabel   = $periodLabel;
        $this->startDate     = $startDate;
        $this->endDate       = $endDate;
        $this->daysInPeriod  = $daysInPeriod;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $sourcesStr = implode(', ', array_column($this->alertItems, 'source'));
        return new Envelope(
            subject: "⚠️ Peringatan Kuota Air Mentah (≥ 80%): {$sourcesStr} [Periode {$this->periodLabel}]",
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
                'periodLabel'   => $this->periodLabel,
                'startDate'     => $this->startDate,
                'endDate'       => $this->endDate,
                'daysInPeriod'  => $this->daysInPeriod,
                // Backwards-compatibility fallback
                'monthName'     => $this->periodLabel,
                'year'          => '',
                'daysInMonth'   => $this->daysInPeriod,
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
