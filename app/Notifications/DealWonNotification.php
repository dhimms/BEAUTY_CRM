<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DealWonNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $deal;

    public function __construct($deal)
    {
        $this->deal = $deal;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Deal Won!',
            'message' => "{$this->deal->sales->name} has just won a deal: {$this->deal->name} (Rp " . number_format($this->deal->value, 0, ',', '.') . ")",
            'url' => route('admin.deals.index'),
            'type' => 'deal_won',
        ];
    }
}
