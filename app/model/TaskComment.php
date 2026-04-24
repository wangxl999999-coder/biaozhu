<?php
namespace app\model;

use think\Model;

class TaskComment extends Model
{
    protected $name = 'task_comments';
    protected $pk = 'id';

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
