<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'location',
        'photo_path',
        'urgency_level',
        'status',
        'official_comment',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function blockchainHash()
    {
        return $this->morphOne(BlockchainHash::class, 'hashable');
    }

    public static function detectUrgency($description, $category)
    {
        $criticalKeywords = ['fire', 'burning', 'explosion', 'severe bleeding', 'unconscious', 'armed', 'shooting'];
        $highKeywords = ['injured', 'accident', 'robbery', 'flood', 'trapped', 'emergency'];
        $mediumKeywords = ['suspicious', 'noise complaint', 'minor injury', 'lost'];

        $lowerDesc = strtolower($description . ' ' . $category);

        foreach ($criticalKeywords as $keyword) {
            if (str_contains($lowerDesc, $keyword)) {
                return 'Critical';
            }
        }

        foreach ($highKeywords as $keyword) {
            if (str_contains($lowerDesc, $keyword)) {
                return 'High';
            }
        }

        foreach ($mediumKeywords as $keyword) {
            if (str_contains($lowerDesc, $keyword)) {
                return 'Medium';
            }
        }

        return 'Low';
    }
}