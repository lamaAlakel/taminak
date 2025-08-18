<?php


namespace App\Services;
use Kreait\Firebase\Factory;

use Kreait\Firebase\Messaging\CloudMessage;

use Kreait\Firebase\Messaging\Notification;

class FirebaseNotificationService
{

    static function sendNotification($title , $body , $deviceToken)
    {
        // Initialize Firebase with service account credentials

        $firebase = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'));

        // Get Firebase Messaging instance

        $messaging = $firebase->createMessaging();


        // Create the notification
        $notification = Notification::create($title, $body);

        // Build the message

        $message = CloudMessage::withTarget('token', $deviceToken)->withNotification($notification);

        try {

            $messaging->send($message);

            return['message' => 'Notification sent successfully'];

        } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
            throw new \Exception('Invalid message: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Error sending notification: ' . $e->getMessage());
        }

    }
    public static function sendNotificationToMultipleDevices($title, $body, array $deviceTokens)
    {
        $firebase = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'));
        $messaging = $firebase->createMessaging();

        $notification = Notification::create($title, $body);
        $message = CloudMessage::new()->withNotification($notification);

        try {
            $sendReport = $messaging->sendMulticast($message, $deviceTokens);

            return [
                'success_count' => $sendReport->successes()->count(),
                'failure_count' => $sendReport->failures()->count(),
                'invalid_tokens' => $sendReport->invalidTokens(),
                'unknown_tokens' => $sendReport->unknownTokens(),
            ];
        } catch (\Exception $e) {
            throw new \Exception(['error' => 'Error sending notifications: ' . $e->getMessage()]);
        }
    }

    public static function sendNotificationToTopic($title, $body, $topic)
    {
        $firebase = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'));
        $messaging = $firebase->createMessaging();

        $notification = Notification::create($title, $body);
        $message = CloudMessage::withTarget('topic', $topic)->withNotification($notification);

        try {
            $messaging->send($message);
            return ['message' => 'Notification sent to topic successfully'];
        } catch (\Exception $e) {
            throw new \Exception(['error' => 'Error sending notification to topic: ' . $e->getMessage()]);
        }
    }

}
