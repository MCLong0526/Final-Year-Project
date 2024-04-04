<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $primaryKey = 'service_id';

    protected $fillable = [
        'service_id',
        'user_id',
        'name',
        'description',
        'type',
        'price_per_hour',
        'availability',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function pictures()
    {
        return $this->hasMany(ServicePicture::class, 'service_id', 'service_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'service_user', 'service_id', 'customer_id')
            ->withPivot('status', 'service_dateTime', 'order_dateTime', 'place_to_service', 'approximated_price', 'remark_customer', 'remark_provider');
    }
}
