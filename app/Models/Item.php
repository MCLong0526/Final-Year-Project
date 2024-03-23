<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $primaryKey = 'item_id';

    protected $fillable = [
        'item_id',
        'user_id',
        'name',
        'description',
        'condition',
        'type',
        'price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function pictures()
    {
        return $this->hasMany(ItemPicture::class, 'item_id', 'item_id');
    }
}
