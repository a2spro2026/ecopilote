<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyGroup extends Model
{
    protected $fillable = [
        'code',
        'matiere',
        'niveau',
        'teacher_id',
    ];

    protected static function booted(): void
    {
        static::created(function (StudyGroup $group) {
            if (blank($group->code)) {
                $group->forceFill([
                    'code' => 'GR-'.str_pad((string) $group->id, 4, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'study_group_student')->orderBy('nom_complet');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(StudySession::class, 'study_group_id');
    }

    public function displayId(): string
    {
        return $this->code ?: ('GR-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT));
    }

    public function effectif(): int
    {
        return $this->students->count();
    }

    public function subjectRevenue(): float
    {
        return round((float) $this->students->sum(fn (Student $student) => $student->subjectFee()), 2);
    }

    public function revenue(): float
    {
        return $this->subjectRevenue();
    }

    public static function money(float $value): string
    {
        return number_format($value, 2, '.', '');
    }

    public function revenueDisplay(): string
    {
        return self::money($this->subjectRevenue());
    }
}
