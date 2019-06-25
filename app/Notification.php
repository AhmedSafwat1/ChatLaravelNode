<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    protected $fillable = ['sender_id',"user_id","message","msg_id","rom"];
    public function User()
    {
        return $this->belongsTo("App\User","user_id");
    }
    public function sender()
    {
        return $this->belongsTo("App\User","sender_id");
    }
    public function Message()
    {
        return $this->belongsTo("App\Message","msg_id");
    }
    public function Room()
    {
        return $this->belongsTo("App\Message","rom");
    }
}
