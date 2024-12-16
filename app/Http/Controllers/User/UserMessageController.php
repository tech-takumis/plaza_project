<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Staff;
use App\Models\Message;
use App\Models\UserMessage;
use Illuminate\Http\Request;
use App\Events\UserChatEvent;
use App\Models\StaffMessages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

class UserMessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return response()->json(DB::select(
            'select * from user_messages where sender_id = ?',
             [$user->id]));
    }
    public function all()
    {
        return response()->json(DB::select(
            'select * from user_message_details'));
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'message' => 'required|string|max:255',
            'sender_id' => 'required|exists:users,id'
        ]);

        $auth = Auth::user();
        $sender = User::find($request->sender_id);
        $receiver = Staff::where('position', 'ILIKE', '%admin%')->first();

        // Create the UserMessage record
        $usermessage = UserMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $request->message
        ]);

        Broadcast(new UserChatEvent($sender, $usermessage,$receiver))->toOthers();

        return response()->json($usermessage);
    }


    public function show($id)
    {
        $message = UserMessage::find($id);

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        return response()->noContent();
    }

    public function update(Request $request, $id)
    {
        $message = UserMessage::find($id);
        $userMessage = UserMessage::find($id);

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        $validated = $request->validate([
            'sender_id' => 'sometimes|integer',
            'message' => 'sometimes|string|max:255',
        ]);

        $message->update($validated);
        return response()->noContent();
    }

    // Delete a user message
    public function destroy($id)
    {
        $message = UserMessage::find($id);

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        $message->delete();
        return response()->noContent();
    }
}
