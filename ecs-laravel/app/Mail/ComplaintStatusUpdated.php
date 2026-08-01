<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to a resident whenever an admin changes the status of their complaint.
 */
class ComplaintStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Complaint $complaint)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on your complaint ' . $this->complaint->reference_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.complaint-status-updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
