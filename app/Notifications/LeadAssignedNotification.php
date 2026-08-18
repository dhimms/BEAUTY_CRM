<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadAssignedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    //membuat instance baru untuk notification
    public $lead;

    //membuat instance baru untuk notification
    public function __construct($lead)
    {
        $this->lead = $lead;
    }

    //menentukan channel mana yang akan digunakan untuk mengirim notifikasi
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    //mengirim data notifikasi ke database
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Lead Assigned',
            'message' => "You have been assigned a new lead: {$this->lead->name}",
            'url' => route('sales.leads.show', $this->lead->id),
            'type' => 'lead_assigned',
        ];
    }

}
