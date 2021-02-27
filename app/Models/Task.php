<?php


namespace App\Models;


class Task extends BaseModel
{
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function children()
    {
        return $this->hasMany(Task::class, 'pid');
    }

    public function taskList()
    {
        return $this->belongsTo(TaskList::class);
    }

    public function parent()
    {
        return $this->hasOne(Task::class, 'id', 'pid');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
