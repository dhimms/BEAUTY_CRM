<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BlastMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageContent;
    public ?string $imagePath; // Path relatif ke storage/app/public/

    /**
     * Create a new message instance.
     *
     * @param string      $messageContent Isi pesan teks
     * @param string|null $imagePath      Path gambar relatif (misal: uploads/blast/abc.jpg)
     */
    public function __construct(string $messageContent, ?string $imagePath = null)
    {
        $this->messageContent = $messageContent;
        $this->imagePath      = $imagePath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Informasi Terbaru dari Kami',
        );
    }

    /**
     * Get the message content definition.
     * Gambar di-embed sebagai inline CID — tidak membutuhkan URL publik.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.blast',
            with: [
                'content'   => $this->messageContent,
                'imagePath' => $this->imagePath,
            ]
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
