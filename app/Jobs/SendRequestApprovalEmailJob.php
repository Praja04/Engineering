<?php

namespace App\Jobs;

use App\Mail\RequestApprovalMail;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendRequestApprovalEmailJob implements ShouldQueue
{
    use Queueable;

    protected $sertifikat;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(KalibrasiSertifikatModel $sertifikat, User $user)
    {
        $this->sertifikat = $sertifikat;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new RequestApprovalMail($this->sertifikat, $this->user->username));
    }
}
