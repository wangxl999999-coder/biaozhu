<?php
namespace app\model;

use think\Model;

class Earnings extends Model
{
    protected $name = 'earnings';
    protected $pk = 'id';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userTask()
    {
        return $this->belongsTo(UserTask::class, 'user_task_id');
    }
}
