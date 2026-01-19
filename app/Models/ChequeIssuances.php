<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChequeIssuances extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'business_name',
        'bank',
        'cheque_number',
        'amount',
        'status',
        'remarks',
        'due_date',
    ];
}
