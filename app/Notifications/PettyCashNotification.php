<?php

namespace App\Notifications;

use App\Models\PettyCash;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PettyCashNotification extends Notification
{
    use Queueable;

    protected $pettyCash;
    protected $type;

    /**
     * Create a new notification instance.
     *
     * @param PettyCash $pettyCash
     * @param string $type ('created', 'ready_for_payment', 'paid')
     */
    public function __construct(PettyCash $pettyCash, string $type)
    {
        $this->pettyCash = $pettyCash;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->getSubject();
        $messageText = $this->getMessage();
        $frontendUrl = rtrim(config('app.frontend_url'), '/');
        $actionUrl = "{$frontendUrl}/petty-cash/" . $this->pettyCash->id;

        // Choose template based on type
        $view = ($this->type === 'ready_for_payment')
            ? 'mails.petty-cash-payment-alert'
            : 'mails.petty-cash-request-update';

        return (new MailMessage)
            ->subject($subject)
            ->view($view, [
                'notifiableName' => $notifiable->name ?? $this->pettyCash->full_name,
                'pettyCash' => $this->pettyCash->load(['category', 'branch', 'department', 'approver', 'payer']),
                'messageText' => $messageText,
                'actionUrl' => $actionUrl,
                'type' => $this->type
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'petty_cash_id' => $this->pettyCash->id,
            'reference_number' => $this->pettyCash->reference_number,
            'type' => $this->type,
            'message' => $this->getMessage(),
        ];
    }

    protected function getSubject(): string
    {
        return match ($this->type) {
            'created' => 'New Petty Cash Received - ' . $this->pettyCash->reference_number,
            'verified' => 'Petty Cash Verified - Waiting for Final Approval - ' . $this->pettyCash->reference_number,
            'approved' => 'Petty Cash Approved - Waiting for Payment Approval - ' . $this->pettyCash->reference_number,
            'rejected' => 'Petty Cash Rejected - ' . $this->pettyCash->reference_number,
            'ready_for_payment' => 'Petty Cash Approved - Ready for Payment - ' . $this->pettyCash->reference_number,
            'paid' => 'Petty Cash Payment Received - ' . $this->pettyCash->reference_number,
            'onhold' => 'Petty Cash Payment On-Hold - ' . $this->pettyCash->reference_number,
            default => 'Petty Cash Notification',
        };
    }

    protected function getMessage(): string
    {
        return match ($this->type) {
            'created' => 'A new petty cash request has been received and is awaiting your review.',
            'verified' => 'A petty cash request has been verified and is now waiting for final approval.',
            'approved' => 'Your petty cash request has been approved and is now waiting for payment approval.',
            'rejected' => 'Your petty cash request has been rejected.',
            'ready_for_payment' => 'A petty cash request has been approved and is now ready for payment processing.',
            'paid' => 'The payment for your petty cash request has been successfully received.',
            'onhold' => 'The payment for your petty cash request has been put on hold.',
            default => 'There is a new update regarding a petty cash request.',
        };
    }
}
