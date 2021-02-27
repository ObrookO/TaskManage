<?php


namespace App\Models;


class TaskList extends BaseModel
{
    protected $table = 'task_list';
    protected $guarded = [];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'task_list_id')->where('tasks.pid', 0);
    }
}
