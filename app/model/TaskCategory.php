<?php
namespace app\model;

use think\Model;

class TaskCategory extends Model
{
    protected $name = 'task_categories';
    protected $pk = 'id';

    public function tasks()
    {
        return $this->hasMany(Task::class, 'category_id');
    }
}
