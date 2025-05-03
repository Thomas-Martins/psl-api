<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;

class MailService
{
    /**
     * Send an email
     *
     * @param string|array $to Recipient email address or array of addresses
     * @param string $subject Email subject
     * @param string $view View template name
     * @param array $data Data to pass to the view
     * @param array $attachments Array of file paths to attach
     * @param array $cc Array of CC email addresses
     * @param array $bcc Array of BCC email addresses
     * @return bool
     */
    public function send(
        string|array $to,
        string $subject,
        string $view,
        array $data = [],
        array $attachments = [],
        array $cc = [],
        array $bcc = []
    ): bool {
        try {
            Mail::send($view, $data, function (Message $message) use ($to, $subject, $attachments, $cc, $bcc) {
                $message->to($to)
                    ->subject($subject);

                if (!empty($cc)) {
                    $message->cc($cc);
                }

                if (!empty($bcc)) {
                    $message->bcc($bcc);
                }

                foreach ($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        $message->attach($attachment);
                    }
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a raw text email
     *
     * @param string|array $to
     * @param string $subject
     * @param string $content
     * @param array $attachments
     * @return bool
     */
    public function sendRaw(
        string|array $to,
        string $subject,
        string $content,
        array $attachments = []
    ): bool {
        try {
            Mail::raw($content, function (Message $message) use ($to, $subject, $attachments) {
                $message->to($to)
                    ->subject($subject);

                foreach ($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        $message->attach($attachment);
                    }
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send raw email: ' . $e->getMessage());
            return false;
        }
    }
} 
