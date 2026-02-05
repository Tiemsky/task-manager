<?php

namespace App\Jobs;

use App\Models\Task;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckOverdueTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        Log::info('Job CheckOverdueTasks démarré');
        /*
        * Get overdue tasks that are not completed and have no completion date
        */
        $overdueTasks = Task::query()
            ->where('due_date', '<', Carbon::today())
            ->where('status', '!=', 'completed')
            ->whereNull('completed_at')
            ->with('user')
            ->get();

        $notifiedCount = 0;
        $skippedCount = 0;

        foreach ($overdueTasks as $task) {
            try {
                /*
                * Check if a notification has already been sent today for this task
                */
                $alreadyNotified = $task->user->notifications()
                    ->where('type', TaskOverdueNotification::class)
                    ->where('data->task_id', $task->id)
                    ->where('created_at', '>=', Carbon::today())
                    ->exists();

                if ($alreadyNotified) {
                    $skippedCount++;
                    Log::info("Notification déjà envoyée aujourd'hui pour la tâche ID {$task->id}");

                    continue;
                }

                /*
                * Calculate reaming days overdue
                */
                $daysOverdue = $task->due_date->diffInDays(Carbon::today());

                /*
                * Send notification to the user
                */
                $task->user->notify(new TaskOverdueNotification($task, $daysOverdue));
                $notifiedCount++;

                Log::info("Notification envoyée pour la tâche ID {$task->id} ({$daysOverdue} jour(s) de retard)");

            } catch (\Exception $e) {
                Log::error("Erreur lors du traitement de la tâche ID {$task->id}: ".$e->getMessage());
            }
        }

        Log::info("Job terminé : {$notifiedCount} notifications envoyées, {$skippedCount} ignorées (déjà notifiées)");
    }
}
