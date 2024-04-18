<?php

namespace Modules\Notification\Services\Firebase;

use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class FCM
{
    /**
     * Send Push Notification to multiple device which identified by given device
     * tokens with given optional notification details and optional data
     *
     * @param  array  $deviceTokens
     * @param  array  $body
     * @param  array  $data
     */
    public function sendMulti($deviceTokens = [], $body = [], $data = [])
    {
        if (empty($deviceTokens)) {
            error_log('No deviceTokens specified.');
            abort(400, 'User Do not Have Device Token specified.');
        } else {
            $credentialPath = config('notification.firebase_credentials');
            $factory = (new Factory)->withServiceAccount($credentialPath);
            $messaging = $factory->createMessaging();

            $message = CloudMessage::new();

            if (! empty($body)) {
                $message = $message->withNotification($body);
            }

            if (! empty($data)) {
                $message = $message->withData($data);
            }

            try {
                $report = $messaging->sendMulticast($message, $deviceTokens);
                error_log('Successful sends: '.$report->successes()->count().PHP_EOL);
                error_log('Failed sends: '.$report->failures()->count().PHP_EOL);

                if ($report->hasFailures()) {
                    foreach ($report->failures()->getItems() as $failure) {
                        error_log($failure->error()->getMessage().PHP_EOL);
                    }
                }
            } catch (MessagingException|FirebaseException $e) {
                error_log($e);
                abort(400, $e->getMessage());
            }
        }
    }
}
