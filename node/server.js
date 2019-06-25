const express = require("express"),
      app = express(),
      http = require('http').Server(app);
      cors    = require("cors"),
      morgan  = require("morgan"),
      port    = 8890,
      db      = require("./util/database"),
      io      = require("socket.io")(http);

//midlware config *******************************************
app.use(cors()) // for confilict the connect other server
app.use(morgan("tiny")) //for logger rote in the screen

//
io.on('connection',function (socket) {
    console.log("new user come",socket.id)
    // join to room
    socket.on('subscribe', function(room) {
        console.log('joining room', room);
        socket.join(room);
    });
    //send message event
    socket.on('send-message', (data)=>{
        // handl recieve msg
        io.sockets.in(data.rom).emit('receive-message', {
            sender_id :data.sender_id,
            nameSender: data.senderName,
            message   : data.message,
            msg : data.msg
        });
        socket.broadcast.to(data.rom).emit("notification",{
            user_id : data.reciever_id,
            sender_id :data.sender_id,
            message :data.message,
            msg_id  : data.msg,
            type    : 1,
            rom     : data.rom,
            notify  :data.notify

        })

    });

    // write event
    socket.on('write', (data)=>{
        // handl recieve msg
        socket.broadcast.to(data.rom).emit('otherWrite', {
           ...data
        });
    });


})
// db.execute("select * from users").then(
//     data => console.log(data),
// ).catch(
//     err => console.log(err.message)
// );
//Run server**************************************************
http.listen(port,()=>console.log(`server running ...  http://localhost:${port} `))