<?php

namespace App\Services;

use App\Models\User;

class SystemNotificationService
{
    protected $firebase;

    public function __construct(FirebaseNotificationService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function notifySeller($status, $sellerId, $data = [])
    {
        return $this->sendNotificationByStatus($status, $sellerId, $data);
    }

    public function notifyBuyer($status, $buyerId, $data = [])
    {
        return $this->sendNotificationByStatus($status, $buyerId, $data);
    }

    public function sendNotificationByStatus(string $status, int $userId, array $extraData = [])
    {
        $user = User::find($userId);

        if (!$user || !$user->fcm_token) {
            return [
                'status' => 'error',
                'message' => 'User not found or FCM token missing.'
            ];
        }

        $notifications = [

            // 🔹 Product Events
            'PRODUCT_SUBMITTED' => [
                'title' => 'Product Submitted ⏳',
                'body'  => "Hello {$user->name}, your product has been submitted and is waiting for admin approval.",
            ],

            'PRODUCT_APPROVED' => [
                'title' => 'Your Ad is Live 🎉',
                'body'  => "Hello {$user->name}, your product is now live on the platform.",
            ],

            'PRODUCT_REJECTED' => [
                'title' => 'Product Rejected ❌',
                'body'  => "Hello {$user->name}, your product has been rejected. Please check dashboard for details.",
            ],

            'PRODUCT_BOOSTED' => [
                'title' => 'Boost Successful 🚀',
                'body'  => "Hello {$user->name}, thank you for boosting your product.",
            ],

            // 🔹 Buyer ↔ Seller Events
            'PRODUCT_LIKED' => [
                'title' => 'Product Liked ❤️',
                'body'  => "Good news {$user->name}! Someone liked your product.",
            ],

            'PRODUCT_VIEWED' => [
                'title' => 'Product Viewed 👀',
                'body'  => "Hi {$user->name}, your product is getting attention!",
            ],

            'OFFER_RECEIVED' => [
                'title' => 'New Offer Received 💰',
                'body'  => "Hello {$user->name}, you have received a new offer on your product.",
            ],

            'OFFER_ACCEPTED' => [
                'title' => 'Offer Accepted 🎉',
                'body'  => "Hello {$user->name}, your offer has been accepted by the seller.",
            ],

            'OFFER_REJECTED' => [
                'title' => 'Offer Rejected ❌',
                'body'  => "Hello {$user->name}, your offer has been rejected by the seller.",
            ],
        ];

        if (!isset($notifications[$status])) {
            return [
                'status' => 'error',
                'message' => 'Invalid status provided.'
            ];
        }

        $notificationData = $notifications[$status];

        return $this->firebase->sendToMessaging([
            'title' => $notificationData['title'],
            'body'  => $notificationData['body'],
            'type'  => $status,
            'extra' => json_encode($extraData)
        ], $user->fcm_token);
    }
}
