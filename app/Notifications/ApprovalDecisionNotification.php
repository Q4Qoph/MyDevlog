<?php

namespace App\Notifications;

use App\Models\Approval;
use App\Models\Feature;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Feature  $feature,
        private Approval $approval,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $approved     = $this->approval->decision === 'approved';
        $featureTitle = $this->feature->title;
        $projectName  = $this->feature->project->name;
        $featureUrl   = route('projects.show', $this->feature->project_id);

        $mail = (new MailMessage)
            ->subject($approved
                ? "Approved: {$featureTitle}"
                : "Changes requested: {$featureTitle}"
            )
            ->greeting($approved ? 'Good news!' : 'Feedback received')
            ->line($approved
                ? "Your client approved **{$featureTitle}** on {$projectName}. You're good to go."
                : "Your client requested changes on **{$featureTitle}** — {$projectName}."
            );

        if ($this->approval->client_note) {
            $mail->line("**Client note:** {$this->approval->client_note}");
        }

        return $mail
            ->action('View feature', $featureUrl)
            ->salutation('— DevLog');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'feature_id'   => $this->feature->id,
            'feature_title'=> $this->feature->title,
            'project_id'   => $this->feature->project_id,
            'decision'     => $this->approval->decision,
            'client_note'  => $this->approval->client_note,
        ];
    }
}
