<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public  function ChatRoomsFrist()
    {
        return $this->hasMany("App\ChatRoom", "first");
    }
    public  function ChatRoomsSecond()
    {
        return $this->hasMany("App\ChatRoom", "Second");
    }
    //message
    public  function SenderMessages()
    {
        return $this->hasMany("App\Message", "sender_id");
    }
    public  function ReciverMessages()
    {
        return $this->hasMany("App\Message", "reciever_id");
    }
    //notifaction
    public  function Notifications()
    {
        return $this->hasMany("App\Notification", "user_id");
    }
    public  function SendNotifications()
    {
        return $this->hasMany("App\Notification", "sender_id");
    }
}
