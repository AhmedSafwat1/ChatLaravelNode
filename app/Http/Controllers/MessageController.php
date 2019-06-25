<?php

namespace App\Http\Controllers;

use App\ChatRoom;
use App\Message;
use App\User;
use Illuminate\Http\Request;
use Validator;
class MessageController extends Controller
{
    //
    public function saveMessage(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'message'        =>"required",
            'sender_id'      => 'required|exists:users,id',
            'reciever_id'    => 'required|exists:users,id',
        ]);
        if ($validator->passes()) {
            $rom = getChatRom($request["sender_id"], $request["reciever_id"]);
            $rom->Messages()->create($request->only("message", "sender_id", "reciever_id"));
            $msg = $request['lang'] == 'ar' ? 'تم ارسال الرساله بنجاح.' : ' sucessfull send message .';
            return response()->json(['key'=>'sucess','value'=>'1',"rom"=>$rom->id, 'msg'=>$msg]);
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['key' => 'fail','value' => 0, 'msg' => $msg[0]]);
                }
            }
        }
    }
    public function getRom(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'sender_id'      => 'required|exists:users,id',
            'reciever_id'    => 'required|exists:users,id',
        ]);
        if ($validator->passes()) {
            $rom = getChatRom($request["sender_id"], $request["reciever_id"]);
            $msg = $request['lang'] == 'ar' ? 'تم  الحصول على الغرفه بنجاح.' : ' sucessfull get rom message.';
            return response()->json(['key'=>'sucess','value'=>'1',"rom"=>$rom->id, 'msg'=>$msg]);
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['key' => 'fail','value' => 0, 'msg' => $msg[0]]);
                }
            }
        }
    }
    // save message in rom
    public function saveMessageRom(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'message'        =>"required",
            'sender_id'      => 'required|exists:users,id',
            'reciever_id'    => 'required|exists:users,id',
            'chat_id'        => 'required|exists:chat_rooms,id'
        ]);
        if ($validator->passes()) {
            $rom = ChatRoom::find($request["chat_id"]);
            $m   =  $rom->Messages()->create($request->only("message", "sender_id", "reciever_id"));
            $data = [
                "user_id"=>$request["reciever_id"],
                "rom"    =>$rom->id,
                "message"=>$request["message"],
                'msg_id' =>$m->id,

            ];
            $msg = $request['lang'] == 'ar' ? 'تم ارسال الرساله بنجاح.' : ' sucessfull send message .';
            $n = \App\User::find($request["sender_id"])->SendNotifications()->create($data);
            return response()->json(['key'=>'sucess','value'=>'1',"rom"=>$rom->id,"notify"=>$n->id,"message"=>$m->id, 'msg'=>$msg]);
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['key' => 'fail','value' => 0, 'msg' => $msg[0]]);
                }
            }
        }
    }
    public function changeMessageStatus(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'message_id'      => 'required|exists:messages,id',
        ]);
        if ($validator->passes()) {
            $message = Message::find($request["message_id"]);
            $message->status = 1;
            $message->update();
            $msg = $request['lang'] == 'ar' ? 'تم مشاهدة الرساله الرساله بنجاح.' : ' sucessfull has seen .';
            return response()->json(['key'=>'sucess','value'=>'1', 'msg'=>$msg]);
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['key' => 'fail','value' => 0, 'msg' => $msg[0]]);
                }
            }
        }
    }
}
