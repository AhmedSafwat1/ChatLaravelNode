@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1>Messages </h1>
                <span id="event"></span>
                <div class="messages" id="msgs">
                    @foreach($messages as $msg)
                        @if($msg->sender->id != $user->id)
                            <p><span>{{$msg->Sender->name}}</span>: {{$msg->message}}</p>
                        @else
                            <p  class='bg-primary text-light'><span>Me</span>: {{$msg->message}}</p>
                        @endif
                        @endforeach
                </div>
                <form>
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Message</label>
                        <textarea class="form-control" id="messageSend" rows="3"></textarea>
                    </div>
                    <button type="submit"  id="send" class="btn btn-primary">Send</button>
                </form>
            </div>
            <div class="col-md-4">
                <h2>User Online</h2>
                <ul>
                    @foreach($useractive as $u)
                        @if($u->id != $user->id)
                        <li>
                            <a href="{{route('rom',["user_id"=>$u->id])}}">{{$u->name}}</a>
                        </li>
                        @endif
                        @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
@section("javscripts")
    <script>
        try {

            var sockit  = io.connect("http://localhost:8890"),
                message =$("#messageSend");
                rom     = {{$chat->id}},
                msgs    =$("#msgs"),
                id      ={{\Auth::user()->id}},
                recive  = {{$reciever->id}},
                not     = 0;
            var audio = new Audio('/sound/slow-spring-board-longer-tail.mp3');
            sockit.on('connect', function(){
                if(rom)
                {
                    sockit.emit('subscribe', rom);
                }

            });
            function sendmessage(sockit,msg,notify)
            {

                sockit.emit("send-message",{
                    rom:rom,
                    sender_id:id,
                    reciever_id:recive,
                    msg ,
                    notify,
                    senderName :"{{\Auth::user()->name}}",
                    message:message.val()
                })
                message.val("");
            }
            // subscribe room

            // function get rom
            function get_rom()
            {
                $.ajax({
                    type:'get',
                    url:'/api/getRom',
                    data:{message:message.val(), sender_id:id, reciever_id:recive},
                    success:function(data){
                        if(data.value == "1")
                        {
                            rom = data.rom;
                            sockit.emit('subscribe', data.rom);
                        }
                        else
                        {
                            console.log(data)
                        }

                    }
                });
            }
            // get_rom()
            //save the message ajex
            function savemessage()
            {
                if(rom !=0)
                {
                    $.ajax({
                        type:'get',
                        url:'/api/saveMessageRom',
                        data:{message:message.val(), sender_id:id, reciever_id:recive,chat_id:rom},
                        success:function(data){
                            if(data.value == "1")
                            {
                                sendmessage(sockit,data.message,data.notify);
                            }
                            else
                            {
                                console.log(data)
                            }

                        }
                    });
                }
            }
            // change message status
            function changeStatusMessage(id)
            {
                $.ajax({
                    type:'get',
                    url:'/api/changeMessageStatus',
                    data:{message_id:id},
                    success:function(data){
                        if(data.value == "1")
                        {
                            console.log("change");
                        }
                        else
                        {
                            console.log(data)
                        }

                    }
                });
            }
            sockit.on("receive-message",(data)=>{
                if(data.sender_id == id)
                {
                    msgs.append(`<p class='bg-primary'><span>Me<span>: ${data.message}</p>`)
                }
                else
                {
                    changeStatusMessage(data.msg)
                    audio.play()
                    msgs.append(`<p><span>${data.nameSender}<span>: ${data.message}</p>`)
                }
                $("#event").text("");

            })
            $("#send").click(function (e) {
                e.preventDefault();
                savemessage()
            })
            message.keypress(function () {
                if(rom !=0)
                {
                    sockit.emit('write', {flag:0,rom,name : "{{\Auth::user()->name}}"});
                }
            })
            sockit.on("otherWrite",(data)=>{
                let  msg = data.flag == 0 ? `${data.name} write .....` : ""
                $("#event").text(msg)
            })
            message.blur(function () {
                sockit.emit('write', {flag:1,rom,name : "{{\Auth::user()->name}}"});
            })
            sockit.on("notification",(data)=>{
               if(data.user_id == id)
               {
                   let number = $("#notify-number");
                   let x =parseInt(number.text())+1;
                   number.text(x);
                   let notify = ` <a class="dropdown-item" href="{{route('open')}}?notify_id=${data.notify}">${data.message.substring(0,12)}</a>`;
                   $("#notify").prepend(notify);
               }
            })
        }
        catch (e) {
            console.log(e.message)
        }
    </script>
@endsection

