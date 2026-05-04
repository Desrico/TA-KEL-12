<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class HighRiskStudentDetected extends Notification
{
    use Queueable;

    public $student;

    /**
     * Create a new notification instance.
     */
    public function __construct($student)
    {
        $this->student = $student;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('⚠️ Peringatan Risiko Tinggi!')
            ->icon('/img/logo.png')
            ->body("Mahasiswa {$this->student->name} ({$this->student->nim}) terdeteksi berada di Level 3 (Krisis). Mohon segera tinjau!")
            ->action('Lihat Detail', 'view_student')
            ->options(['action_url' => url("/konselor/detail/{$this->student->nim}")]);
    }
}
