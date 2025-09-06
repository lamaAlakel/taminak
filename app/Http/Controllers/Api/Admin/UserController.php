<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index()
    {
        return response()->json(
            User::all()
        );
    }
    /**
     * Block a user: set blocked = true and revoke all tokens.
     */
    public function block($id)
    {
        $user = User::findOrFail($id);
        $user->update(['blocked' => 1]);
        $user->tokens()->delete();  // revoke existing tokens

        // Send notification if user has fcm_token
        if (!empty($user->fcm_token)) {
            FirebaseNotificationService::sendNotification(
                'Account Blocked',
                'Your account has been blocked. Please contact support for assistance.',
                $user->fcm_token
            );
        }

        return response()->json(['message' => 'User blocked successfully.']);
    }

    /**
     * Unblock a user.
     */
    public function unblock($id)
    {
        $user = User::findOrFail($id);
        $user->update(['blocked' => 0]);

        // Send notification if user has fcm_token
        if (!empty($user->fcm_token)) {
            FirebaseNotificationService::sendNotification(
                'Account Unblocked',
                'Your account has been reactivated. You can now log in again.',
                $user->fcm_token
            );
        }

        return response()->json(['message' => 'User unblocked successfully.']);
    }

    /**
     * Permanently delete a user (and revoke tokens).
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->tokens()->delete();
        $user->delete();
        return response()->json(['message' => 'User deleted successfully.'], 200);
    }


}
