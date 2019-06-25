<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    //
    function updateMessageStatus($message , $user_id)
    {
        if($message->reciever_id == $user_id);
    }
    function index(Request $request)
    {

        $this->validate($request,[
            "user_id" => "required|exists:users,id"
        ]);
        $user      = \Auth::user();
        $reciever  = \App\User::find($request["user_id"]);
        $chat      = getChatRom($user->id, $reciever->id);
        $useractive=\App\User::whereActive(0)->get();
        $notifications = $user->Notifications()->where("status",0);
        $messages = $chat->Messages;
        $chat->Messages()->where("reciever_id",$user->id)->update(["status"=>1]);
        return view("ChatRoom",compact("user","reciever","notifications","chat","messages","useractive"));
    }
    function openNotification(Request $request)
    {
        $this->validate($request,[
            "notify_id" => "required|exists:notifications,id"
        ]);
        $notify = Notification::findOrfail($request["notify_id"]);
        $notify->status = 1;
        $notify->update();
        return redirect()->route('rom', ['user_id' => $notify->sender_id]);

    }
}
