<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsGateway extends Model
{
    use HasFactory;
    protected $table = 'sms_gateway';
    protected $fillable = [
       'sender_id', 'status','sid','token','api_url', 'created_at', 'updated_at'
    ];

}