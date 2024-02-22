<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction',
        'code',
        'state',
        'owner_id',
        'amount',
        'reason',
        'payme_time',
        'cancel_time',
        'create_time',
        'perform_time',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
