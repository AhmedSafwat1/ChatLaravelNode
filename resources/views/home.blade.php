@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Messages </h1>
            <span id="event"></span>
            <p class="messages" id="msgs">

            </p>
            <form>
                <div class="form-group">
                    <label for="exampleFormControlTextarea1">Message</label>
                    <textarea class="form-control" id="messageSend" rows="3"></textarea>
                </div>
                <button type="submit"  id="send" class="btn btn-primary">Send</button>
            </form>
        </div>
        <div class="col-md-4">
           Chat
        </div>
    </div>
</div>
@endsection
@section("javscripts")
    <script>
    try {



        var sockit  = io.connect("http://localhost:8890"),
            message =$("#messageSend");
            rom     = 0,
            msgs    =$("#msgs"),
            id      ={{\Auth::user()->id}};
            recive  = id == 1?2:1
    function sendmessage(sockit,msg)
    {

        sockit.emit("send-message",{
            rom:rom,
            sender_id:id,
            reciever_id:recive,
            msg ,
            senderName :"{{\Auth::user()->name}}",
            message:message.val()
        })
        message.val("");
    }
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
        get_rom()
        //save the message ajex
        function savemessage()
        {
            if(rom !=0)
            {
                $.ajax({
                    type:'get',
                    url:'/api/saveMessageRom',
                    data:{message:message.val(), sender_id:1, reciever_id:2,chat_id:rom},
                    success:function(data){
                        if(data.value == "1")
                        {
                            sendmessage(sockit,data.message);
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
            msgs.append(`<p><span>Me<span>: ${data.message}</p>`)
        }
        else
        {
            changeStatusMessage(data.msg)
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
    }
    catch (e) {
        console.log("d")
    }
    </script>
@endsection

