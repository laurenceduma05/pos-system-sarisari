<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'by',
        'lyrics',
        'chords',
        'key',
        'link'
    ];

    protected $casts = [
        'chords' => 'array', // If you store chords as JSON
    ];
}
