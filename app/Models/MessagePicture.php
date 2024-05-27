<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessagePicture extends Model
{
    use HasFactory;

    protected $table = 'message_pictures';

    protected $primaryKey = 'picture_id';

    protected $fillable = [
        'picture_id',
        'message_id',
        'picture_path',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }
}
