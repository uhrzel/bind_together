<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApproveReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $commentOwnerName;
    public $commentTitle;


    public function __construct($commentOwnerName, $commentTitle)
    {
        $this->commentOwnerName = $commentOwnerName;
        $this->commentTitle = $commentTitle;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->subject('Notice of Comments Removal Due to Community Guidelines Violation')
                    ->view('mail.comment_removal_notification');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
