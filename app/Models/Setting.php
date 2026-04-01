<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
class Setting extends Model
{
    protected $fillable = ['key', 'value'];
    /**
     * Отримує значення налаштування та кешує його.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            return self::where('key', $key)->first()?->value ?? $default;
        });
    }
    /**
     * Оновлює значення налаштування та очищує кеш.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
    }
}