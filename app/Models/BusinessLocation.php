<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'location_id',
        'name',
        'landmark',
        'city',
        'zip_code',
        'state',
        'country',
        'mobile',
        'alternate_number',
        'email',
        'website',
        'is_active',
    ];

    protected $casts = [
        'mobile'    => 'array',   // stored as JSON array of phone numbers
        'is_active' => 'boolean',
    ];

    /**
     * Get the business that owns this location.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get users assigned to this location.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Auto-generate the next location_id (BL0001, BL0002…).
     */
    public static function generateNextLocationId(): string
    {
        $last = static::orderByDesc('id')->value('location_id');

        if ($last) {
            $num = (int) substr($last, 2); // e.g. "BL0003" → 3
            return 'BL' . str_pad($num + 1, 4, '0', STR_PAD_LEFT);
        }

        return 'BL0001';
    }
}
