<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['filename', 'path', 'caption'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
