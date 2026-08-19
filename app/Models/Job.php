<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title',
    'description',
    'required_skills',
    'category',
    'location',
    'work_type',
    'salary',
    'deadline',
])]
class Job extends Model
{
    /** @use HasFactory<\Database\Factories\JobFactory> */
    use HasFactory;

    protected $table = 'job_listings';

    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
            'deadline' => 'date',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_listing_id');
    }

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'applications', 'job_listing_id', 'user_id')
            ->withPivot(['status', 'cover_letter', 'created_at'])
            ->withTimestamps();
    }

    public function requiredSkillsList(): array
    {
        return collect(preg_split('/[,\n]+/', (string) $this->required_skills))
            ->map(fn ($s) => strtolower(trim($s)))
            ->filter()
            ->values()
            ->all();
    }

    public function isOpen(): bool
    {
        return $this->deadline >= today();
    }

    public function workTypeLabel(): string
    {
        return match ($this->work_type) {
            'remote' => 'Remote',
            'hybrid' => 'Hybrid',
            default => 'On-site',
        };
    }
}
