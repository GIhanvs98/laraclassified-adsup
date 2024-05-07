<?php

namespace App\Mail;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdReviewResponsed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected Post $post,
        protected string $reason,
    )
    {        
        $this->afterCommit();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address(config('mail.reply_to.address'), config('mail.reply_to.name')),
            ],
            subject: 'Your ad "'.$this->post->title.'" needs to be edited',
            tags: ['ad-reviews', 'submitted'],
            metadata: [
                'ad_review_id' => $this->post->reviewingViolation->id
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.ad-review-responsed',
            with: [
                'name' => $this->post->contact_name,
                'title' => $this->post->title,
                'reason' => $this->reason,
                'homePage' => config('app.url'),
                'adEditPage' => route('post-ad.edit', ['post' => $this->post->id]),
                'appName' => config('app.name'),
            ],
        );
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
