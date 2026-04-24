<?php
namespace app\model;

use think\Model;

class UserTask extends Model
{
    protected $name = 'user_tasks';
    protected $pk = 'id';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
