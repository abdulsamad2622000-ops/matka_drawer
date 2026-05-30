<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'amount', 'screenshot_path',
        'transaction_reference', 'status', 'admin_note', 'approved_by', 'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'amount'       => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}