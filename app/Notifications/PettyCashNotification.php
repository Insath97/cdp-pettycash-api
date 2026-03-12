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
        $actionUrl = url('/petty-cash/' . $this->pettyCash->id);

        // Choose template based on type
        $view = ($this->type === 'ready_for_payment')
            ? 'mails.petty-cash-payment-alert'
            : 'mails.petty-cash-request-update';

        return (new MailMessage)
            ->subject($subject)
            ->view($view, [
                'notifiableName' => $notifiable->name,
                'pettyCash' => $this->pettyCash->load('category'),
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
            'created' => 'New Petty Cash Request Submitted',
            'ready_for_payment' => 'Petty Cash Approved - Ready for Payment',
            'paid' => 'Petty Cash Payment Completed',
            default => 'Petty Cash Notification',
        };
    }

    protected function getMessage(): string
    {
        return match ($this->type) {
            'created' => 'A new petty cash request has been submitted and is awaiting your review.',
            'ready_for_payment' => 'A petty cash request has been approved and is now ready for payment processing.',
            'paid' => 'The payment for the petty cash request has been successfully processed.',
            default => 'There is a new update regarding a petty cash request.',
        };
    }
}
