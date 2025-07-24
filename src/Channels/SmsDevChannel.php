<?php

namespace Skeylup\LaravelSmsDev\Channels;

use Illuminate\Notifications\Notification;
use Skeylup\LaravelSmsDev\Messages\SmsDevMessage;
use Skeylup\LaravelSmsDev\Models\SmsLog;

class SmsDevChannel
{
    /**
     * Envoyer la notification donnée.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        // Récupérer le message SMS depuis la notification
        $message = $notification->toSms($notifiable);

        // Si ce n'est pas un SmsDevMessage, on essaie de le convertir
        if (! $message instanceof SmsDevMessage) {
            $message = SmsDevMessage::create($message);
        }

        // Récupérer le numéro de téléphone du destinataire
        $to = $this->getTo($notifiable, $notification);

        if (! $to) {
            return; // Pas de numéro de téléphone, on ne peut pas envoyer
        }

        // Enregistrer le SMS dans la base de données
        SmsLog::create([
            'to' => $to,
            'from' => $message->from,
            'message' => $message->content,
            'metadata' => [
                'notification_class' => get_class($notification),
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->getKey(),
                'notifiable_name' => $notifiable->name ?? null,
                'notifiable_email' => $notifiable->email ?? null,
                'channel' => 'sms-dev',
                'original_data' => $message->toArray(),
            ],
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Récupérer le numéro de téléphone du destinataire
     */
    protected function getTo(object $notifiable, Notification $notification): ?string
    {
        // Essayer d'abord la méthode routeNotificationForSms
        if (method_exists($notifiable, 'routeNotificationForSms')) {
            return $notifiable->routeNotificationForSms($notification);
        }

        // Essayer ensuite la méthode routeNotificationForSmsdev
        if (method_exists($notifiable, 'routeNotificationForSmsdev')) {
            return $notifiable->routeNotificationForSmsdev($notification);
        }

        // Essayer les attributs communs
        return $notifiable->phone ?? $notifiable->mobile ?? $notifiable->phone_number ?? null;
    }
}
