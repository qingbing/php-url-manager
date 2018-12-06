<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2018-11-19
 * Version      :   1.0
 */

namespace UrlManager;

use Abstracts\BaseUrlRule;
use UrlManager;

class UrlRule extends BaseUrlRule
{
    /* @var array 追加默认参数 */
    public $defaultParams = [];
    /* @var array 从路由中提取的替换引用["module"=>"<module>", "controller"=>"<controller>",  "action"=>"<action>", ...] */
    public $references = [];
    /* @var array 从pattern中提取的GET参数，排除了 $this->references */
    public $params = [];
    /* @var bool URL-GET参数是否允许自行增加，如果pattern末尾带有"*"表示允许增加，否则不允许 */
    public $append;
    /* @var string 由 url-pathinfo 解析到路由时的正则表达式 */
    public $pattern;
    /* @var string 路由串 */
    public $route; // 路由
    /* @var string 创建URL时用到的正则表达式 */
    public $routePattern;
    /* @var string URL 模版: <module>/<controller>/view/<action>/<id> */
    public $template; // url模板

    /**
     * UrlRule constructor.
     * @param array $config
     * @param UrlManager $urlManager
     */
    public function __construct(array $config = [], UrlManager $urlManager)
    {
        $route = $config['route'];
        $pattern = $config['pattern'];
        unset($config['route'], $config['pattern']);
        if (!empty($config)) {
            $this->configure($config);
        }
        // 设置路由
        $this->route = trim($route, '/');

        $tr2['/'] = $tr['/'] = '\\/';
        // 解析 route，找出需要替换的, 匹配 $route 中 <\w+> 内容
        if (preg_match_all('#<(\w+)>#', $route, $m)) {
            foreach ($m[1] as $name) {
                $this->references[$name] = "<$name>";
            }
        }

        // 匹配 $pattern 中 <\w+:?((.*?)?)> 内容
        if (preg_match_all('#<(\w+):?(.*?)?>#', $pattern, $m)) {
            $ms = array_combine($m[1], $m[2]);
            foreach ($ms as $name => $value) {
                if ('' === $value) { // 匹配 <controller>
                    $value = '[^\/]+';
                }
                $tr["<$name>"] = $pOption = "(?P<{$name}>{$value})";
                if (isset($this->references[$name])) {
                    $tr2["<$name>"] = $pOption;
                } else {
                    $this->params[$name] = $value;
                }
            }
        }
        $template = rtrim($pattern, '*');
        $this->append = $template !== $pattern;

        $this->template = preg_replace('/<(\w+):?.*?>/', '<$1>', trim($template, '/'));
        $pattern = '#^' . strtr($this->template, $tr) . '\/';
        if ($this->append) {
            $pattern .= '#u';
        } else {
            $pattern .= '$#u';
        }
        $this->pattern = $pattern;
        if ([] !== $this->references) {
            if ($urlManager->caseSensitive) {// 区分大小写
                $this->routePattern = '#^' . strtr($route, $tr2) . '$#u';
            } else {
                $this->routePattern = '#^' . strtr($route, $tr2) . '$#ui';
            }
        }
    }

    /**
     * 解析Url
     * @param UrlManager $urlManager
     * @param string $pathInfo
     * @return mixed
     */
    public function parsePathInfo(UrlManager $urlManager, $pathInfo)
    {
        $pathInfo .= '/';
        if (preg_match($this->pattern, $pathInfo, $ms)) {
            foreach ($this->defaultParams as $name => $value) {
                if (!isset($_GET[$name])) {
                    $_REQUEST[$name] = $_GET[$name] = $value;
                }
            }
            $tr = [];
            foreach ($ms as $key => $value) {
                if (isset($this->references[$key])) { // 路由
                    $tr[$this->references[$key]] = $value;
                } elseif (isset($this->params[$key])) { // GET 参数
                    $_REQUEST[$key] = $_GET[$key] = $value;
                }
            }
            if ($pathInfo !== $ms[0]) { // 解析传统的 $_GET 参数
                $urlManager->parsePathInfo(ltrim(substr($pathInfo, strlen($ms[0])), '/'));
            }
            if ($this->routePattern !== null) {
                // 替换找到对应的action
                return strtr($this->route, $tr);
            } else {
                return $this->route;
            }
        }
        return false;
    }

    /**
     * 创建URl
     * @param UrlManager $urlManager
     * @param string $route 路由
     * @param array $params $_GET参数
     * @param string $ampersand 锚点信息
     * @return string
     */
    public function createPathInfo(UrlManager $urlManager, $route, $params, $ampersand)
    {
        $tr = [];
        if ($route !== $this->route) {
            if (null !== $this->routePattern && preg_match($this->routePattern, $route, $m)) {
                foreach ($this->references as $key => $name) {
                    $tr[$name] = $m[$key];
                }
            } else {
                return false;
            }
        }
        // 默认参数
        foreach ($this->defaultParams as $key => $value) {
            if (isset($params[$key])) { // 一旦设置必须和配置的一致
                if ($params[$key] == $value) {
                    unset($params[$key]);
                } else {
                    return false;
                }
            }
        }
        // 规定参数必须设置，否则不匹配该规则
        foreach ($this->params as $key => $value) {
            if (!isset($this->params[$key])) {
                return false;
            }
            $tr["<$key>"] = urlencode($params[$key]);
            unset($params[$key]);
        }
        $url = strtr($this->template, $tr);
        if (empty($params)) {
            return $url !== '' ? $url . $urlManager->urlSuffix : $url;
        }
        // 还有未在pattern中出现的参数
        if ($this->append) {
            // 允许pathInfo 追加
            $url .= '/' . $urlManager->createPathInfo($params, '/', '/') . $urlManager->urlSuffix;
        } else {
            // 不允许追加时，参数放在 "?" 后面
            if ($url !== '') {
                $url .= $urlManager->urlSuffix;
            }
            $url .= '?' . $urlManager->createPathInfo($params, '=', $ampersand);
        }
        return $url;
    }
}
