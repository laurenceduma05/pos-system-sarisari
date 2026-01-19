<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teaching extends Model
{
    use HasFactory;

    protected $table = 'teachings'; 

    protected $fillable = [
        'uploader',
        'title',
        'descriptions',
        'file_path',
        'file_type',
        'audience',
        'link',
    ];

 // Cast audience field to an array for easier access
    protected $casts = [
        'audience' => 'array',
    ];

     /**
     * Get the file URL if it exists
     * 
     * @return string|null
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return asset('storage/' . $this->file_path); // Assuming files are stored in the 'public' disk
        }

        return null;
    }

        /**
     * Check if the teaching has a specific audience
     *
     * @param string $audience
     * @return bool
     */
    public function hasAudience($audience)
    {
        return in_array($audience, $this->audience);
    }

     /**
     * Scope query to filter teachings by a specific audience
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $audience
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByAudience($query, $audience)
    {
        return $query->whereJsonContains('audience', $audience);
    }
}
