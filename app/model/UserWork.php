<?php
namespace app\model;

use think\Model;

class UserWork extends Model
{
    protected $name = 'user_work';
    protected $pk = 'id';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
