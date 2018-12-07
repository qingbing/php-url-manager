<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2018-12-06
 * Version      :   1.0
 */

namespace Abstracts;

use UrlManager;

abstract class BaseUrlRule extends Base
{
    /**
     * 创建URl
     * @param UrlManager $urlManager
     * @param string $route 路由
     * @param array $params $_GET参数
     * @param string $ampersand 锚点信息
     * @return string
     */
    abstract public function createPathInfo(UrlManager $urlManager, $route, $params, $ampersand);

    /**
     * 解析Url
     * @param UrlManager $urlManager
     * @param string $pathInfo
     * @return mixed
     */
    abstract public function parsePathInfo(UrlManager $urlManager, $pathInfo);
}