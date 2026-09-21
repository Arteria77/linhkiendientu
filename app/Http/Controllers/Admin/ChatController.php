<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class ChatController extends Controller
{
    /**
     * Lấy danh sách những User đã từng nhắn tin với Admin
     */
    public function getUsers()
    {
        try {
            // Lấy ID của tất cả người dùng (role = user) từng gửi hoặc nhận tin nhắn
            $userIds = Message::pluck('sender_id')
                ->merge(Message::pluck('receiver_id'))
                ->unique()
                ->reject(fn($id) => $id == Auth::id());

            $users = User::whereIn('id', $userIds)->select('id', 'name', 'email')->get();

            return response()->json($users);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Lấy tin nhắn giữa Admin và một User cụ thể
     */
    public function getMessages($userId)
    {
        try {
            $adminId = Auth::id();

            $messages = Message::where(function ($q) use ($adminId, $userId) {
                $q->where('sender_id', $adminId)->where('receiver_id', $userId);
            })->orWhere(function ($q) use ($adminId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $adminId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

            return response()->json($messages);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Admin gửi tin nhắn cho User
     */
    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        try {
            $message = Message::create([
                'sender_id'   => Auth::id(),
                'receiver_id' => $request->user_id,
                'content'     => trim($request->message),
                'is_read'     => true,
            ]);

            return response()->json($message);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}