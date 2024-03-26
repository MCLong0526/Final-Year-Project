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
        'quantity',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function pictures()
    {
        return $this->hasMany(ItemPicture::class, 'item_id', 'item_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'item_user', 'item_id', 'user_id')
            ->withPivot('status', 'meet_dateTime', 'order_dateTime', 'remark_buyer', 'remark_seller', 'quantity', 'place_to_meet');
    }
}
