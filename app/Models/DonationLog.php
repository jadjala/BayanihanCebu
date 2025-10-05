<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'official_id',
        'distributed_to',
        'quantity_distributed',
        'proof_photo',
        'remarks',
    ];

    protected $casts = [
        'quantity_distributed' => 'decimal:2',
    ];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function official()
    {
        return $this->belongsTo(User::class, 'official_id');
    }
}