<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['product_id', 'user_id'];

    // The person who STARTED the chat (buyer / customer)
    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // The product this chat is about
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // All messages in this chat
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Optional helper: the seller (product owner)
    public function seller()
    {
        return $this->product->user();   // assumes Product has user() relationship
    }
}