<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'contact_info',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function incidentReports()
    {
        return $this->hasMany(IncidentReport::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'donor_id');
    }

    public function verifiedReports()
    {
        return $this->hasMany(IncidentReport::class, 'verified_by');
    }

    public function verifiedDonations()
    {
        return $this->hasMany(Donation::class, 'verified_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function donationLogs()
    {
        return $this->hasMany(DonationLog::class, 'official_id');
    }

    public function isResident()
    {
        return $this->role === 'resident';
    }

    public function isDonor()
    {
        return $this->role === 'donor';
    }

    public function isOfficial()
    {
        return $this->role === 'official';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}