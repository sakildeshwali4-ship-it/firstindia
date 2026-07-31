<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    protected $fillable = [
        'title',
        'type',
        'media_url',
        'media_type',
        'click_url',
        'start_after_seconds',
        'repeat_every_seconds',
        'duration_seconds',
        'skippable_after_seconds',
        'priority',
        'active',
        'start_date',
        'end_date'
    ];

    public function getStartAfterSecondsAttribute($value)
    {
        return self::normalizeStartAfterSeconds($value);
    }

    public function getStartAfterSecondsDisplayAttribute()
    {
        return implode(',', self::normalizeStartAfterSeconds($this->attributes['start_after_seconds'] ?? null));
    }

    public static function normalizeStartAfterSeconds($value)
    {
        if (is_array($value)) {
            return array_values(array_map('intval', $value));
        }

        if ($value === null || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_map('intval', $decoded));
        }

        return array_values(array_map('intval', preg_split('/\s*,\s*/', (string) $value, -1, PREG_SPLIT_NO_EMPTY)));
    }
}
