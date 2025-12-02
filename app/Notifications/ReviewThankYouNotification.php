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
    public $reviewerName;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $reviewSnapshot = [], $notifiable = null)
    {
        $this->reviewSnapshot = $reviewSnapshot;
        $this->reviewerName = $notifiable->name ?? 'Reviewer';
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
        $frontendUrl = config('app.frontend_url') ?: config('app.url');
        
        return (new MailMessage)
            ->subject('Terima kasih atas ulasan Anda!')
            ->view('emails.review-thank-you', [
                'reviewerName' => $this->reviewerName,
                'productName' => $this->reviewSnapshot['product_name'] ?? 'Produk',
                'reviewText' => $this->reviewSnapshot['comment'] ?? 'Ulasan Anda',
                'rating' => isset($this->reviewSnapshot['rating']) ? (int) $this->reviewSnapshot['rating'] : null,
                'reviewUrl' => $frontendUrl . '/products/' . ($this->reviewSnapshot['product_slug'] ?? $this->reviewSnapshot['product_id'] ?? '#'),
                'shopUrl' => $frontendUrl . '/catalog',
                'helpUrl' => url('/help'),
                'privacyUrl' => url('/privacy'),
            ]);
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
