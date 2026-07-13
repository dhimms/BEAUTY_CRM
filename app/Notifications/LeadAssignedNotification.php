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
    public $lead;

    public function __construct($lead)
    {
        $this->lead = $lead;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

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
