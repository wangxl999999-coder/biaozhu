<?php
namespace app;

use think\App;
use think\exception\ValidateException;
use think\Validate;

abstract class BaseController
{
    protected $app;

    protected $middleware = [];

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->initialize();
    }

    protected function initialize()
    {
    }

    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                list($validate, $scene) = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        if (!$v->batch($batch)->check($data)) {
            throw new ValidateException($v->getError());
        }

        return true;
    }
}
