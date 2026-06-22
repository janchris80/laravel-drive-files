<?php

namespace Janchris80\DriveFiles\Models;

// Optional dependency shim — keeps the model working without spatie/laravel-activitylog.
if (! trait_exists(\Spatie\Activitylog\Traits\LogsActivity::class)) {
    if (! class_exists(\Spatie\Activitylog\LogOptions::class)) {
        eval('namespace Spatie\\Activitylog; class LogOptions {
            public static function defaults(): self { return new self(); }
            public function logOnlyDirty(): self { return $this; }
            public function logFillable(): self { return $this; }
        }');
    }
    eval('namespace Spatie\\Activitylog\\Traits; trait LogsActivity {
        public function getActivitylogOptions(): \\Spatie\\Activitylog\\LogOptions {
            return \\Spatie\\Activitylog\\LogOptions::defaults();
        }
    }');
}

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DriveFile extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'name',
        'google_file_id',
        'google_drive_folder_id',
        'mime_type',
        'size_bytes',
        'visibility',
        'public_link',
        'category',
        'uploaded_by_user_id',
        'meta',
    ];

    protected $casts = [
        'meta'       => 'array',
        'size_bytes' => 'integer',
    ];

    public function getTable(): string
    {
        return config('drive-files.table_name', 'drive_files');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(config('drive-files.models.user'), 'uploaded_by_user_id');
    }

    public function scopePublic(Builder $q): Builder
    {
        return $q->where('visibility', 'public');
    }

    public function scopeInCategory(Builder $q, string $category): Builder
    {
        return $q->where('category', $category);
    }

    public function scopeForUser(Builder $q, int $userId): Builder
    {
        return $q->where('uploaded_by_user_id', $userId);
    }

    public function getSizeKbAttribute(): float
    {
        return round(($this->size_bytes ?? 0) / 1024, 2);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
