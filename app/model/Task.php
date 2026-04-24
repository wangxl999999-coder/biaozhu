<?php
namespace app\model;

use think\Model;

class Task extends Model
{
    protected $name = 'tasks';
    protected $pk = 'id';

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'category_id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id');
    }

    public function userTasks()
    {
        return $this->hasMany(UserTask::class, 'task_id');
    }

    public function getTagListAttr($value)
    {
        return $this->tags ? explode(',', $this->tags) : [];
    }
}
