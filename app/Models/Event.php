<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'date',
        'time',
        'location',
        'contact_phone',
        'contact_email',
        'organizer_id',
    ];

    public function organizer()
    {
        // Satu event hanya dimiliki oleh satu organizer
        return $this->belongsTo(Organizer::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
