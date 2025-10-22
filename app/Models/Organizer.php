<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function events()
    {
        // Satu organizer bisa memiliki banyak event
        return $this->hasMany(Event::class);
    }
}
