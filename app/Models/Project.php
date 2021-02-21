<?php


namespace App\Models;


class Project extends BaseModel
{
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
