<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotas extends Model
{
    use HasFactory;

    protected $fillable = ['quota_limit', 'issued', 'day_start'];

    public static function getQuotaByDueDate($dueDate)
    {
        return self::whereDate('day_start','=', $dueDate->toDateString())->first();
    }

    public static function getCurrentQuota()
    {
        // this code is getting the quota per week
       /* return self::whereDate('week_start', '<=', now())
            ->whereDate('week_start', '>', now()->subweek()) // Week range check
            ->first(); // Assumes only one quota record per week
        */

        return self::whereDate('created_at', '=', now()->toDateString())
            ->first();
    }

    public static function getStartOfDayQuota()
    {
        return self::whereDate('day_start', '=', now()->toDateString())
        ->first();
    }

    public function isQuotaExceeded($amount)
    {
        return ($this->issued + $amount) > $this->quota_limit;
    }

    public function resetQuota()
    {
        $this->issued = 0;
        $this->save();
    }
}
