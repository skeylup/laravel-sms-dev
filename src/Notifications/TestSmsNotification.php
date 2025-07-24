<?php

namespace Skeylup\LaravelSmsDev\Notifications;

use Illuminate\Notifications\Notification;
use Skeylup\LaravelSmsDev\Messages\SmsDevMessage;

class TestSmsNotification extends Notification
{
    public function __construct(
        public string $message = 'Ceci est un SMS de test depuis le package SMS Dev!'
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['sms-dev'];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): SmsDevMessage
    {
        return SmsDevMessage::create($this->message)
            ->from(config('sms-dev.default_from', 'SMS-DEV'));
    }
}
