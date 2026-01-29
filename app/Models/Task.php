<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'due_date', 'status', 'category_id', 'user_id', 'completed_at'];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sharedWith(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'list_shares', 'task_id', 'shared_with_user_id');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function toggleStatus(): void
    {
        $this->completed_at = $this->completed_at === null ? now() : null;
        $this->save();
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date < now() && !$this->isCompleted();
    }
}
