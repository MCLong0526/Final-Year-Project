<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePicture extends Model
{
    use HasFactory;

    protected $table = 'service_pictures';

    protected $primaryKey = 'picture_id';

    protected $fillable = [
        'picture_id',
        'service_id',
        'picture_path',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }
}
