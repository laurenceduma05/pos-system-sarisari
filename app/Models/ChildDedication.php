<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildDedication extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_firstname',
        'child_middlename',
        'child_lastname',
        'child_dob',
        'child_pob',
        'child_gender',
        'father_fullname',
        'mother_fullname',
        'contact_number',
        'address',
        'date_of_dedication',
        'venue',
        'officiating_pastor',
        'sponsors',
    ];

    protected $cast = [
        // 'sponsors' => 'array', // If you store sponsors as JSON
    ];
}
