<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
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
        // Log for debugging: notification construction
        $key = method_exists($notifiable, 'getKey') ? $notifiable->getKey() : ($notifiable->id ?? 'unknown');
        Log::info('Building SellerApproved mail for user_key=' . $key);

        // Prefer a signed URL provided by the caller (Admin controller). If not provided,
        // generate a temporary signed route for 'seller.verify' using the notifiable's key.
        if (!empty($this->signedUrl)) {
            $activateUrl = $this->signedUrl;
        } else {
            $activateUrl = URL::temporarySignedRoute(
                'seller.verify', now()->addDays(7), ['seller' => $key]
            );
        }

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
