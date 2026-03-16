<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Get all notifications for logged-in customer
    public function index()
    {
        $customerId = Auth::guard('customer-api')->id();

        $notifications = Notification::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    // Mark a notification as read
    public function markAsRead($id)
    {
        $customerId = Auth::guard('customer-api')->id();

        $notification = Notification::where('id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.'
            ], 404);
        }

        $notification->update(['read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
            'notification' => $notification
        ]);
    }

    // Delete a notification
    public function destroy($id)
    {
        $customerId = Auth::guard('customer-api')->id();

        $notification = Notification::where('id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.'
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully.'
        ]);
    }

    // Optional: Create or update a payment notification
    public static function createOrUpdatePaymentNotification($payment, $status)
    {
        $message = $status === 'Approved' ? 'Your payment has been approved.' : 'Your payment has been rejected.';

        $notification = Notification::where('payment_id', $payment->id)->first();

        if ($notification) {
            $notification->update([
                'type' => 'payment_status',
                'message' => $message,
                'read' => false,
            ]);
        } else {
            Notification::create([
                'customer_id' => $payment->customer_id,
                'payment_id' => $payment->id,
                'type' => 'payment_status',
                'message' => $message,
                'read' => false,
            ]);
        }
    }
}