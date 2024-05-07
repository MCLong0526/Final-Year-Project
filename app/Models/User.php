<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'username',
        'phone_number',
        'email',
        'password',
        'status',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class, 'user_id', 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_user', 'buyer_id', 'item_id')
            ->withPivot('status', 'meet_dateTime', 'order_dateTime', 'remark_buyer', 'remark_seller', 'quantity', 'place_to_meet');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_user', 'customer_id', 'service_id')
            ->withPivot('status', 'service_dateTime', 'order_dateTime', 'remark_customer', 'approximated_price', 'remark_provider', 'place_to_service');
    }

    // User.php
    public function following()
    {
        return $this->belongsToMany(User::class, 'following', 'user_id', 'following_id')->withTimestamps();
    }
}
