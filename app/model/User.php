<?php
namespace app\model;

use think\Model;

class User extends Model
{
    protected $name = 'users';
    protected $pk = 'id';

    public function education()
    {
        return $this->hasMany(UserEducation::class, 'user_id');
    }

    public function work()
    {
        return $this->hasMany(UserWork::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(UserTask::class, 'user_id');
    }

    public function earnings()
    {
        return $this->hasMany(Earnings::class, 'user_id');
    }
}
