<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewThankYouNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $reviewSnapshot;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $reviewSnapshot = [])
    {
        $this->reviewSnapshot = $reviewSnapshot;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage);

        $mail->line('Terima kasih atas review Anda.');

        if (!empty($this->reviewSnapshot['product_name'])) {
            $mail->line('Produk: ' . $this->reviewSnapshot['product_name']);
        }
        if (!empty($this->reviewSnapshot['rating'])) {
            $mail->line('Rating: ' . $this->reviewSnapshot['rating']);
        }

        // Build product detail URL using product slug
        $productUrl = url('/');
        if (!empty($this->reviewSnapshot['product_slug'])) {
            $frontendUrl = config('app.frontend_url') ?: config('app.url');
            $productUrl = $frontendUrl . '/products/' . $this->reviewSnapshot['product_slug'];
        }

        $mail->action('Kunjungi Produk', $productUrl)
             ->line('Terima kasih sudah menggunakan aplikasi kami!');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
