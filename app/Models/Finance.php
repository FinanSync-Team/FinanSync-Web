<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'category',
        'source',
        'amount'
    ];

    protected $casts = [
        'amount' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
