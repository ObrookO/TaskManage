<?php


namespace App\Models;


class User extends BaseModel
{
    protected $guarded = [];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
