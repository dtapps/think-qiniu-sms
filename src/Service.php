<?php

declare (strict_types=1);

namespace dtapps\qiniu\sms;

use think\App;
use think\Container;

/**
 * 自定义服务基类
 * Class Service
 * @package asw
 */
abstract class Service
{
    /**
     * 应用实例
     * @var App
     */
    protected $app;

    /**
     * Service constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->initialize();
    }

    /**
     * 初始化服务
     * @return static
     */
    protected function initialize(): Service
    {
        return $this;
    }

    /**
     * 静态实例对象
     * @param array $var 实例参数
     * @param boolean $new 创建新实例
     * @return static
     */
    public static function instance(array $var = [], bool $new = false): Service
    {
        return Container::getInstance()->make(static::class, $var, $new);
    }
}