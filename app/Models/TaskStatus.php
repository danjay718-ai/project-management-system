<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    protected $fillable = [
        'name',
        'label',
        'color',
        'position',
    ];

    /**
     * Get the tasks with this status.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
