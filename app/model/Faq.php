<?php
namespace app\model;

use think\Model;

class Faq extends Model
{
    protected $name = 'faqs';
    protected $pk = 'id';

    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }
}
