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
    //membuat instance baru untuk notification
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
    //menentukan channel mana yang akan digunakan untuk mengirim notifikasi
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    //mengirim data notifikasi ke database
    public function toDatabase(object $notifiable): array
    {
        $url = $notifiable->hasRole('Manager') 
            ? route('manager.pipeline.index') 
            : route('admin.deals.index');

        return [
            'title' => 'Deal Won!',
            'message' => "{$this->deal->assignedUser?->name} berhasil memenangkan deal: {$this->deal->name}",
            'url' => $url,
            'type' => 'deal_won',
        ];
    }
}
