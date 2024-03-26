<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPicture extends Model
{
    use HasFactory;

    protected $table = 'item_pictures';

    protected $primaryKey = 'picture_id';

    protected $fillable = [
        'picture_id',
        'item_id',
        'picture_path',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }
}
