<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public int $daysOverdue
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $priorityColors = [
            'low' => '#10B981',    // Vert
            'medium' => '#F59E0B', // Orange
            'high' => '#EF4444',   // Rouge
        ];

        $priorityColor = $priorityColors[$this->task->priority] ?? '#6B7280';
        $priorityLabel = match($this->task->priority) {
            'low' => 'Basse',
            'medium' => 'Moyenne',
            'high' => 'Haute',
        };

        return (new MailMessage)
            ->subject('Date d\'échéance dépassée - ' . $this->task->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('**Une tâche importante nécessite votre attention !**')
            ->line('')
            ->line('La date d\'échéance de votre tâche suivante est dépassée :')
            ->line('')
            ->line('**Titre :** ' . $this->task->title)
            ->line('**Date d\'échéance :** ' . $this->task->due_date->format('d/m/Y'))
            ->line('**Jours de retard :** ' . $this->daysOverdue . ' jour(s)')
            ->line('**Priorité :** <span style="color: ' . $priorityColor . '; font-weight: bold;">' . $priorityLabel . '</span>')
            ->when($this->task->description, function ($mail) {
                return $mail->line('**Description :** ' . $this->task->description);
            })
            ->line('')
            ->action('Voir la tâche', route('tasks.show', $this->task))
            ->line('')
            ->line('Merci de traiter cette tâche dès que possible.')
            ->salutation('Cordialement,');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
