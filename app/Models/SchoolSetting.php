<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model {
    protected $table = 'school_settings';

    protected $fillable = ['key', 'value', 'label'];

    /** Ambil value berdasarkan key, kembalikan $default jika tidak ada. */
    public static function get(string $key, string $default = ''): string {
        return static::where('key', $key)->value('value') ?? $default;
    }

    /** Set (upsert) value berdasarkan key. */
    public static function set(string $key, string $value): void {
        static::where('key', $key)->update(['value' => $value]);
    }
}
