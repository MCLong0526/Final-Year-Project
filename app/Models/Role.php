<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role'; // Assuming role is the table name

    protected $primaryKey = 'role_id'; // Assuming role_id is the primary key

    protected $fillable = ['name']; // Assuming name is fillable

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
