<?php

namespace App\Http\Controllers\Staff;

use App\Models\Staff;
use App\Models\Message;
use App\Events\MessageEvent;
use Illuminate\Http\Request;
use App\Models\StaffMessages;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StaffMessagesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'sender_id'=> ['required','exists:staff,id'],
            'message'=>['required','string'],
            'receiver_id' => ['required','exists:staff,id'],
        ]);

        $auth = Auth::guard('staff')->user();
        $sender = Staff::find($auth->id);
        $receiver = Staff::find($request->receiver_id);

        $staffMessage = StaffMessages::create([
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);


        Broadcast(new MessageEvent($sender ,$receiver,$staffMessage))->toOthers();

        return response()->noContent();
    }

    public function index(){

        $auth = Auth::guard('staff')->user();
        $message = DB::select('select * from staff_message_details where sender_id = ?',[$auth->id]);
        return response()->json($message);
    }
}
