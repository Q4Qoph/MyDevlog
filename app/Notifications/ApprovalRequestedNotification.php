<?php

namespace App\Notifications;

use App\Models\Feature;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Feature $feature,
        private string  $clientEmail,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $approvalUrl  = route('approval.show', $this->feature->approval_token);
        $projectName  = $this->feature->project->name;
        $featureTitle = $this->feature->title;
        $devName      = $notifiable->name;

        return (new MailMessage)
            ->subject("Review requested: {$featureTitle} — {$projectName}")
            ->greeting("Hi,")
            ->line("{$devName} would like your approval before working on a new feature.")
            ->line("**Feature:** {$featureTitle}")
            ->when($this->feature->description, fn ($mail) =>
                $mail->line($this->feature->description)
            )
            ->action('Review & approve', $approvalUrl)
            ->line("This link is unique to this request. You can approve or request changes — no account needed.")
            ->salutation("— DevLog");
    }
}
