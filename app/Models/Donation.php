<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'type',
        'quantity',
        'value',
        'destination',
        'status',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'value' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function logs()
    {
        return $this->hasMany(DonationLog::class);
    }

    public function blockchainHash()
    {
        return $this->morphOne(BlockchainHash::class, 'hashable');
    }

    public function getRemainingQuantityAttribute()
    {
        $distributed = $this->logs()->sum('quantity_distributed');
        return $this->quantity - $distributed;
    }
}