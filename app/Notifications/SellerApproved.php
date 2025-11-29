<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

class SellerApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public $sellerSnapshot;
    public $signedUrl;

    public function __construct(array $sellerSnapshot = [], string $signedUrl = null)
    {
        $this->sellerSnapshot = $sellerSnapshot;
        $this->signedUrl = $signedUrl;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Generate signed URL for activation with user ID
        $activateUrl = URL::signedRoute('seller.activate', [
            'user_id' => $notifiable->id
        ]);

        return (new MailMessage)
            ->subject('Selamat! Toko Anda telah aktif.')
            ->view('emails.seller-approved', [
                'sellerName' => $notifiable->name,
                'storeName' => $this->sellerSnapshot['store_name'] ?? $this->sellerSnapshot['company_name'] ?? 'Toko Anda',
                'activateUrl' => $activateUrl,
                'helpUrl' => url('/help'),
                'privacyUrl' => url('/privacy'),
            ]);
    }
}
