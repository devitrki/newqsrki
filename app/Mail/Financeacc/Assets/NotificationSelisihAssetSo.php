<?php

namespace App\Mail\Financeacc\Assets;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Log;

class NotificationSelisihAssetSo extends Mailable
{
    use Queueable, SerializesModels;

    public $periode;
    public $file;
    public $dear;

    public $subjectMail;
    public $toEmails;
    public $ccEmails;
    public $data = [];
    public $attachments = [];


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($periode, $subject, $toEmails, $ccEmails, $file, $dear)
    {
        $this->periode = $periode;
        $this->file = $file;
        $this->dear = $dear;

        $this->subjectMail = $subject;
        $this->toEmails = $toEmails;
        $this->ccEmails = $ccEmails;

        $this->init();
    }

    public function init()
    {
        $this->data = [
            'periode' => $this->periode,
            'dear' => $this->dear
        ];
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->subjectMail,
            to: 'yudhapermana.dev@gmail.com',
            // to: $this->toEmails,
            // cc: $this->ccEmails
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.financeacc.assets.notification-selisih',
            with: $this->data
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [
            Attachment::fromStorage('public/' . $this->file['path'] . $this->file['filename'])
        ];
    }
}
