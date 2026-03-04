<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'receiver_id',
        'message',
        'read_flag',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'read_flag' => 'boolean', // makes $message->read_flag behave like true/false
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Convenience accessor
    public function getIsReadAttribute()
    {
        return (bool) $this->read_flag;
    }
}