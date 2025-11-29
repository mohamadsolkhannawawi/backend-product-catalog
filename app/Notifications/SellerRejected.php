<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class SellerRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $reason;
    public $sellerSnapshot;

    public function __construct(?string $reason = null, array $sellerSnapshot = [])
    {
        $this->reason = $reason;
        $this->sellerSnapshot = $sellerSnapshot;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $reapplyUrl = url('/register');

        return (new MailMessage)
            ->subject('Pembaruan status pendaftaran toko Anda.')
            ->view('emails.seller-rejected', [
                'sellerName' => $notifiable->name,
                'storeName' => $this->sellerSnapshot['store_name'] ?? $this->sellerSnapshot['company_name'] ?? 'Toko Anda',
                'rejectionReason' => $this->reason ?? 'Data pendaftaran tidak memenuhi kriteria kami.',
                'reapplyUrl' => $reapplyUrl,
                'helpUrl' => url('/help'),
                'privacyUrl' => url('/privacy'),
            ]);
    }
}