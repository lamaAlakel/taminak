<?php
// app/Http/Controllers/Api/NotificationController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification as NotificationModel;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function sendToUser(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => ['required', 'exists:users,id'],
                'title'   => ['required', 'string', 'max:255'],
                'body'    => ['required', 'string', 'max:2000'],
            ]);

            $user = User::find($data['user_id']);

            DB::beginTransaction();

            $notification = NotificationModel::create([
                'user_id' => $user->id,
                'title'   => $data['title'],
                'body'    => $data['body'],
            ]);

            $sent = false;
            $sendReport = null;

            $token = $user->fcm_token ?? null; // make sure your users table has `fcm_token`

            if (!empty($token)) {
                try {
                    $sendReport = FirebaseNotificationService::sendNotification(
                        $data['title'],
                        $data['body'],
                        $token
                    );
                    $sent = true;
                } catch (\Throwable $e) {
                    // Firebase send failed, but keep the created DB record
                    Log::error('FCM send error: ' . $e->getMessage(), [
                        'user_id' => $user->id,
                        'notification_id' => $notification->id,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status'       => true,
                'message'      => $sent
                    ? 'Notification stored and sent successfully.'
                    : (empty($token)
                        ? 'Notification stored. User has no FCM token.'
                        : 'Notification stored. Sending via FCM failed (see logs).'),
                'data'         => [
                    'notification' => $notification,
                    'fcm_sent'     => $sent,
                    'send_report'  => $sendReport,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('sendToUser failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status'  => false,
                'message' => 'Unexpected error while sending notification.',
            ], 500);
        }
    }
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->get();
        return response()->json($notifications);
    }
}
