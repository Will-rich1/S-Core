<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';
    
    protected $fillable = [
        'key_name',
        'value',
        'data_type',
        'description',
        'is_public',
        'updated_by'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get a setting value by key, with optional default
     */
    public static function getSetting($keyName, $default = null)
    {
        $setting = self::where('key_name', $keyName)->first();
        
        if (!$setting) {
            return $default;
        }

        // Cast value based on data_type
        return match($setting->data_type) {
            'integer' => (int)$setting->value,
            'boolean' => $setting->value === '1' || $setting->value === 'true',
            'json' => json_decode($setting->value, true),
            default => $setting->value
        };
    }

    /**
     * Set a setting value by key
     */
    public static function setSetting($keyName, $value, $dataType = 'string', $description = null, $userId = null)
    {
        return self::updateOrCreate(
            ['key_name' => $keyName],
            [
                'value' => is_array($value) ? json_encode($value) : (string)$value,
                'data_type' => $dataType,
                'description' => $description,
                'updated_by' => $userId
            ]
        );
    }
}
