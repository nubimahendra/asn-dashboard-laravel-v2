<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $table = 'app_settings';
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, $default = null)
    {
        $setting = self::find($key);
        return $setting ? $setting->value : $default;
    }

    public static function setValue(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
