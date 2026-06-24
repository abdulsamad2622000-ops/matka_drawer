<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'user_name',
        'reason',
        'status',
        'generated_password',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}