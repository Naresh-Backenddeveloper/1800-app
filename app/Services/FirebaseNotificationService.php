<?php

namespace App\Services;

use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseNotificationService
{
    protected Messaging $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendToMessaging(array $messageData, ?string $token = null): array
    {
        try {

            $notification = Notification::create(
                $messageData['title'] ?? '',
                $messageData['body'] ?? ''
            );

            $cloudMessage = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($messageData);

            $response = $this->messaging->send($cloudMessage);

            return [
                'target' => $token,
                'status' => 'success',
                'response' => $response,
            ];
        } catch (\Throwable $e) {
            return [
                'target' => $token,
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }
}
