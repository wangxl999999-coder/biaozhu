<?php
namespace app\model;

use think\Model;

class FaqCategory extends Model
{
    protected $name = 'faq_categories';
    protected $pk = 'id';

    public function faqs()
    {
        return $this->hasMany(Faq::class, 'category_id');
    }
}
