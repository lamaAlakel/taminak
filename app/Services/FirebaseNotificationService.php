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

        $firebase = (new Factory)->withServiceAccount([
              "type"=> "service_account",
              "project_id"=> "taminak-66ba7",
              "private_key_id"=> "2a124754bbf29fd9448b3a9258a0e0fda98f101e",
              "private_key"=> "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCmeqKDZIhlRtir\nNTukIeJOKSZNjkBlLgugEmUZh9u5kMlLEryTjRtrYwW1GN2zolhc/YQcqNnRGX3V\nu7143PgnDSA1GziubOJgaj6Fo5YcoiwdMJMrZvKZ3wNWMaZN5nAdIC57sgGVlLP6\nuuZDHDobbqPGdSTkFkN7Pk0bBf4Ee67nBAOkGWBxQ28so83voSj8RcHM5GdGkbhY\ngeQAdwReBQCiETH3mf6PUpC7Uns5jbe57ZXf6hUUvb2MoJ+UUVU1wa6Xw2BQXdMg\nvX3fDyevQHMdtAJ4jO/cdIbct55tf7oeYxtFfeiczoyW21QInklie5fXjiMoGq1/\nDhJr/Ka1AgMBAAECggEAB0tjJgszD8ROLO2eRqI7x9gf5xuMdkfRd2rUQQ1geGMf\n8s5oHWCm2tUh/vikiYuw6qgquv+/Wt0OqV1lH1qH9AbBn8S+KSRjueebLuCyAeCt\nj/zczJ9251JBKOxwK0R61Uffnt4NRDgvDQArTor6KnwLUzWMT4MrOJyhpRq63Igw\nTufwKj6rMLZkJ8G0NAiFuIq7FhGOqlketpuO9+EqYuGSXPTxrU42S/RPCtEsI7r7\nOTrv9ivB1WW0lPQfxvohqoPQRjd8yNpLSztWfdO2VyjwNNcWv8brYBJWlKWOUfqH\nste09htjnyuRpwIlTpn0+TR4AiA+FdWefBws9Vhw4QKBgQDl5QBiisRlZ8KRFSwi\nxI4RzaIxfrnDFIny5DYZMKDWb8mqMXusyvbxqj7ktd+mnIZKdcv6IeFhJVN4Tt8K\n0a4zE3gRRKv5bFJ0oHY0k83tw0fr3oVCnXMBwPUe7PYfzvyVvX4eE76iFjpP1y/i\n4tdGzPAyfRwJmLEVPqmrntJGYQKBgQC5YiecD4DRdaJmRsnj59QPwThA5gUS4gP9\n6P+GizWY/MoHfO0Y+/EGU9fQ28Y70ef39rpRr8fB0kKBF0UUBP6tUf3i1CYmCpoZ\nYrxoozg8qN5Sp04bclIh9qUjyHz6kinfdH5WCnfHkG9zAvNwbVz1znbSDh5t2+Y6\nFPZNqM8Y1QKBgB3hQ8mLC7e6YrmErPXf4i9CMOCJ/g7y7nMZYkan4V+9q9JpGA//\nWVVTsVT8ppL7KSEoaliaara7qTur1KWNVzzgPOzRpiCHCbiK2pI+27cZeJGZnGxq\nFnaV48YfOmYr+vxjYq76Ff7vMKkL6PduUFitblLBnBFLPUjrmhXqUJ5BAoGAWNCj\ntWrgBnnbMjDb7KpWuK2Ta9RHvgI2c2E7epQbO45wpCG/3eZmVOe6T7/bOzGXdv7n\nyNb0VvGLFxm06KBz+0l63z9Qf404wszBA0ifWsB0sxignRmqK8dk2r3k0o9QLFQ9\naq9dL2kNXAaf5s8eaE1gM8a74Vom9NCyKAamIXkCgYEA1Vq26iLTS8RPMpnRKcI1\njbgM1xOHAWXs4h8lYbWSp+BOF4KieqqDrzEFJiuXZO9mGMWDF5pQMS2s2kyfOIVT\n2Xxn7tiygAPOMsHlD3Qau5XYxGO/2zFngOufRRTqHQzzJXO083yP2hWW3YS8Ntgu\nnOmv0fMtLqwHbjGJ9hx08CE=\n-----END PRIVATE KEY-----\n",
              "client_email"=> "firebase-adminsdk-fbsvc@taminak-66ba7.iam.gserviceaccount.com",
              "client_id"=> "100057556691782459212",
              "auth_uri"=> "https://accounts.google.com/o/oauth2/auth",
              "token_uri"=> "https://oauth2.googleapis.com/token",
              "auth_provider_x509_cert_url"=> "https://www.googleapis.com/oauth2/v1/certs",
              "client_x509_cert_url"=> "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40taminak-66ba7.iam.gserviceaccount.com",
              "universe_domain"=> "googleapis.com"
        ]);

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
        $firebase = (new Factory)->withServiceAccount([
            "type"=> "service_account",
            "project_id"=> "taminak-66ba7",
            "private_key_id"=> "2a124754bbf29fd9448b3a9258a0e0fda98f101e",
            "private_key"=> "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCmeqKDZIhlRtir\nNTukIeJOKSZNjkBlLgugEmUZh9u5kMlLEryTjRtrYwW1GN2zolhc/YQcqNnRGX3V\nu7143PgnDSA1GziubOJgaj6Fo5YcoiwdMJMrZvKZ3wNWMaZN5nAdIC57sgGVlLP6\nuuZDHDobbqPGdSTkFkN7Pk0bBf4Ee67nBAOkGWBxQ28so83voSj8RcHM5GdGkbhY\ngeQAdwReBQCiETH3mf6PUpC7Uns5jbe57ZXf6hUUvb2MoJ+UUVU1wa6Xw2BQXdMg\nvX3fDyevQHMdtAJ4jO/cdIbct55tf7oeYxtFfeiczoyW21QInklie5fXjiMoGq1/\nDhJr/Ka1AgMBAAECggEAB0tjJgszD8ROLO2eRqI7x9gf5xuMdkfRd2rUQQ1geGMf\n8s5oHWCm2tUh/vikiYuw6qgquv+/Wt0OqV1lH1qH9AbBn8S+KSRjueebLuCyAeCt\nj/zczJ9251JBKOxwK0R61Uffnt4NRDgvDQArTor6KnwLUzWMT4MrOJyhpRq63Igw\nTufwKj6rMLZkJ8G0NAiFuIq7FhGOqlketpuO9+EqYuGSXPTxrU42S/RPCtEsI7r7\nOTrv9ivB1WW0lPQfxvohqoPQRjd8yNpLSztWfdO2VyjwNNcWv8brYBJWlKWOUfqH\nste09htjnyuRpwIlTpn0+TR4AiA+FdWefBws9Vhw4QKBgQDl5QBiisRlZ8KRFSwi\nxI4RzaIxfrnDFIny5DYZMKDWb8mqMXusyvbxqj7ktd+mnIZKdcv6IeFhJVN4Tt8K\n0a4zE3gRRKv5bFJ0oHY0k83tw0fr3oVCnXMBwPUe7PYfzvyVvX4eE76iFjpP1y/i\n4tdGzPAyfRwJmLEVPqmrntJGYQKBgQC5YiecD4DRdaJmRsnj59QPwThA5gUS4gP9\n6P+GizWY/MoHfO0Y+/EGU9fQ28Y70ef39rpRr8fB0kKBF0UUBP6tUf3i1CYmCpoZ\nYrxoozg8qN5Sp04bclIh9qUjyHz6kinfdH5WCnfHkG9zAvNwbVz1znbSDh5t2+Y6\nFPZNqM8Y1QKBgB3hQ8mLC7e6YrmErPXf4i9CMOCJ/g7y7nMZYkan4V+9q9JpGA//\nWVVTsVT8ppL7KSEoaliaara7qTur1KWNVzzgPOzRpiCHCbiK2pI+27cZeJGZnGxq\nFnaV48YfOmYr+vxjYq76Ff7vMKkL6PduUFitblLBnBFLPUjrmhXqUJ5BAoGAWNCj\ntWrgBnnbMjDb7KpWuK2Ta9RHvgI2c2E7epQbO45wpCG/3eZmVOe6T7/bOzGXdv7n\nyNb0VvGLFxm06KBz+0l63z9Qf404wszBA0ifWsB0sxignRmqK8dk2r3k0o9QLFQ9\naq9dL2kNXAaf5s8eaE1gM8a74Vom9NCyKAamIXkCgYEA1Vq26iLTS8RPMpnRKcI1\njbgM1xOHAWXs4h8lYbWSp+BOF4KieqqDrzEFJiuXZO9mGMWDF5pQMS2s2kyfOIVT\n2Xxn7tiygAPOMsHlD3Qau5XYxGO/2zFngOufRRTqHQzzJXO083yP2hWW3YS8Ntgu\nnOmv0fMtLqwHbjGJ9hx08CE=\n-----END PRIVATE KEY-----\n",
            "client_email"=> "firebase-adminsdk-fbsvc@taminak-66ba7.iam.gserviceaccount.com",
            "client_id"=> "100057556691782459212",
            "auth_uri"=> "https://accounts.google.com/o/oauth2/auth",
            "token_uri"=> "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url"=> "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url"=> "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40taminak-66ba7.iam.gserviceaccount.com",
            "universe_domain"=> "googleapis.com"
        ]);
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
        $firebase = (new Factory)->withServiceAccount([
            "type"=> "service_account",
            "project_id"=> "taminak-66ba7",
            "private_key_id"=> "2a124754bbf29fd9448b3a9258a0e0fda98f101e",
            "private_key"=> "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCmeqKDZIhlRtir\nNTukIeJOKSZNjkBlLgugEmUZh9u5kMlLEryTjRtrYwW1GN2zolhc/YQcqNnRGX3V\nu7143PgnDSA1GziubOJgaj6Fo5YcoiwdMJMrZvKZ3wNWMaZN5nAdIC57sgGVlLP6\nuuZDHDobbqPGdSTkFkN7Pk0bBf4Ee67nBAOkGWBxQ28so83voSj8RcHM5GdGkbhY\ngeQAdwReBQCiETH3mf6PUpC7Uns5jbe57ZXf6hUUvb2MoJ+UUVU1wa6Xw2BQXdMg\nvX3fDyevQHMdtAJ4jO/cdIbct55tf7oeYxtFfeiczoyW21QInklie5fXjiMoGq1/\nDhJr/Ka1AgMBAAECggEAB0tjJgszD8ROLO2eRqI7x9gf5xuMdkfRd2rUQQ1geGMf\n8s5oHWCm2tUh/vikiYuw6qgquv+/Wt0OqV1lH1qH9AbBn8S+KSRjueebLuCyAeCt\nj/zczJ9251JBKOxwK0R61Uffnt4NRDgvDQArTor6KnwLUzWMT4MrOJyhpRq63Igw\nTufwKj6rMLZkJ8G0NAiFuIq7FhGOqlketpuO9+EqYuGSXPTxrU42S/RPCtEsI7r7\nOTrv9ivB1WW0lPQfxvohqoPQRjd8yNpLSztWfdO2VyjwNNcWv8brYBJWlKWOUfqH\nste09htjnyuRpwIlTpn0+TR4AiA+FdWefBws9Vhw4QKBgQDl5QBiisRlZ8KRFSwi\nxI4RzaIxfrnDFIny5DYZMKDWb8mqMXusyvbxqj7ktd+mnIZKdcv6IeFhJVN4Tt8K\n0a4zE3gRRKv5bFJ0oHY0k83tw0fr3oVCnXMBwPUe7PYfzvyVvX4eE76iFjpP1y/i\n4tdGzPAyfRwJmLEVPqmrntJGYQKBgQC5YiecD4DRdaJmRsnj59QPwThA5gUS4gP9\n6P+GizWY/MoHfO0Y+/EGU9fQ28Y70ef39rpRr8fB0kKBF0UUBP6tUf3i1CYmCpoZ\nYrxoozg8qN5Sp04bclIh9qUjyHz6kinfdH5WCnfHkG9zAvNwbVz1znbSDh5t2+Y6\nFPZNqM8Y1QKBgB3hQ8mLC7e6YrmErPXf4i9CMOCJ/g7y7nMZYkan4V+9q9JpGA//\nWVVTsVT8ppL7KSEoaliaara7qTur1KWNVzzgPOzRpiCHCbiK2pI+27cZeJGZnGxq\nFnaV48YfOmYr+vxjYq76Ff7vMKkL6PduUFitblLBnBFLPUjrmhXqUJ5BAoGAWNCj\ntWrgBnnbMjDb7KpWuK2Ta9RHvgI2c2E7epQbO45wpCG/3eZmVOe6T7/bOzGXdv7n\nyNb0VvGLFxm06KBz+0l63z9Qf404wszBA0ifWsB0sxignRmqK8dk2r3k0o9QLFQ9\naq9dL2kNXAaf5s8eaE1gM8a74Vom9NCyKAamIXkCgYEA1Vq26iLTS8RPMpnRKcI1\njbgM1xOHAWXs4h8lYbWSp+BOF4KieqqDrzEFJiuXZO9mGMWDF5pQMS2s2kyfOIVT\n2Xxn7tiygAPOMsHlD3Qau5XYxGO/2zFngOufRRTqHQzzJXO083yP2hWW3YS8Ntgu\nnOmv0fMtLqwHbjGJ9hx08CE=\n-----END PRIVATE KEY-----\n",
            "client_email"=> "firebase-adminsdk-fbsvc@taminak-66ba7.iam.gserviceaccount.com",
            "client_id"=> "100057556691782459212",
            "auth_uri"=> "https://accounts.google.com/o/oauth2/auth",
            "token_uri"=> "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url"=> "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url"=> "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40taminak-66ba7.iam.gserviceaccount.com",
            "universe_domain"=> "googleapis.com"
        ]);
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
