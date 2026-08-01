<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Complaint $complaint)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update on Complaint '.$this->complaint->reference_number)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('There is an update on the report you submitted to the Barangay Electronic Complaint System.')
            ->line('Reference Number: '.$this->complaint->reference_number)
            ->line('New Status: '.$this->complaint->status_label)
            ->when($this->complaint->admin_remarks, function (MailMessage $mail) {
                $mail->line('Remarks: '.$this->complaint->admin_remarks);
            })
            ->action('View Complaint Details', route('complaints.show', $this->complaint))
            ->line('Thank you for helping us keep our community safe and clean.');
    }
}
