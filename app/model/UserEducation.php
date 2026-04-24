<?php
namespace app\model;

use think\Model;

class UserEducation extends Model
{
    protected $name = 'user_education';
    protected $pk = 'id';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
