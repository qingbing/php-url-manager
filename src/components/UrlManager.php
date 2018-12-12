<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2018-12-12
 * Version      :   1.0
 */

namespace Components;


use Abstracts\Component;
use Abstracts\Store;
use Helper\Exception;
use Helper\Unit;
use UrlManagerSupports\UrlRule;

class UrlManager extends Component
{
    const CACHE_KEY = 'urlManager.rules';
    const GET_FORMAT = 'get';
    const PATH_FORMAT = 'path';
    /* @var string cache 实例的实例化字符串代码 */
    public $cacheInstanceString = "";
    /* @var array 路由规则 */
    public $rules = [];
    /* @var bool 当需要解析或创建的 URL 不再定义的规则上时是否抛出异常 */
    public $throwNotMatchRule = false;

    /* @var bool 链接的地址是否区分大小写 */
    public $caseSensitive = false;
    /* @var string path 的获取方式，get和path两种 */
    public $urlFormat = 'get';
    /* @var string get 模式下路由的标志 */
    public $routeVar = 'r';
    /* @var string path 模式下链接的后缀名 */
    public $urlSuffix = '.shtml';
    /* @var bool path 创建URL访问链接时是否显示脚本名 */
    public $showScriptName = true;
    /* @var bool 参数是否增加在 pathInfo 后，当 throwNotMatchRule=false 且route不再规则时生效 */
    public $appendParams = true;

    /* @var Store */
    protected $cache;
    /* @var \Abstracts\BaseUrlRule[] */
    private $_rules = [];
    /* @var string 页面的baseUrl */
    private $_baseUrl;

    /**
     * 初始化
     * throw \Exception
     */
    public function init()
    {
        // 缓存实例代码是否设置
        if (!empty($this->cacheInstanceString)) {
            $cache = str_eval($this->cacheInstanceString);
            if (isset($cache) && $cache instanceof Store) {
                $this->cache = $cache;
            }
        }

        // urlRule 预处理
        if (null === $this->cache) {
            $this->_rules = $this->parseUrlRule();
        } else if (null !== ($rules = $this->cache->get(self::CACHE_KEY))) {
            $this->_rules = $rules;
        } else {
            $this->_rules = $this->parseUrlRule();
            $this->cache->set(self::CACHE_KEY, $this->_rules);
        }
    }

    /**
     * 实例化 urlRule
     * @return array
     */
    protected function parseUrlRule()
    {
        $R = [];
        foreach ($this->rules as $rule) {
            $R[] = new UrlRule($rule, $this);
        }
        return $R;
    }

    /**
     * 解析用户URl请求
     * @return string 返回 规则中的路由或 pathInfo
     * @throws \Exception
     */
    public function parseUrl()
    {
        if (self::PATH_FORMAT === $this->urlFormat) {
            $pathInfo = $this->removeUrlSuffix(Request::httpRequest()->getPathInfo(), $this->urlSuffix);
        } elseif (isset($_GET[$this->routeVar])) {
            $pathInfo = $this->removeUrlSuffix($_GET[$this->routeVar], $this->urlSuffix);
        } elseif (isset($_POST[$this->routeVar])) {
            $pathInfo = $_POST[$this->routeVar];
        } else {
            $pathInfo = '';
        }
        foreach ($this->_rules as $i => $rule) {
            $_pathinfo = $rule->parsePathInfo($this, $pathInfo);
            if (false !== $_pathinfo) {
                return $_pathinfo;
            }
        }
        if ($this->throwNotMatchRule) {
            // 不匹配规则时抛出异常
            throw new Exception(str_cover('"{pathInfo}"找不到对应的URL解析规则，不能确定路由', [
                '{pathInfo}' => $pathInfo,
            ]), 100900101);
        } else {
            // 没有匹配的规则，直接返回pathinfo本身
            return $pathInfo;
        }
    }


    /**
     * 移除pathInfo的URL的后缀
     * @param string $pathInfo
     * @param string $urlSuffix
     * @return string
     */
    protected function removeUrlSuffix($pathInfo, $urlSuffix)
    {
        if ('' !== $urlSuffix && substr($pathInfo, -strlen($urlSuffix)) === $urlSuffix) {
            return substr($pathInfo, 0, -strlen($urlSuffix));
        } else {
            return $pathInfo;
        }
    }

    /**
     * 从pathInfo(已经通过urlRule去掉了route 部分)中解析传统的 URL 参数，将对应的参数保存到 $_GET 和 $_REQUEST 中
     * @param string $pathInfo
     */
    public function parsePathInfo($pathInfo)
    {
        if ($pathInfo === '') {
            return;
        }
        $segs = explode('/', $pathInfo . '/');
        $n = count($segs);
        for ($i = 0; $i < $n - 1; $i += 2) {
            $key = $segs[$i];
            if ($key === '') {
                continue;
            }
            $value = $segs[$i + 1];
            if (false !== ($pos = strpos($key, '[')) && ($m = preg_match_all('/\[(.*?)\]/', $key, $ms)) > 0) {
                $name = substr($key, 0, $pos);
                for ($j = $m - 1; $j >= 0; --$j) {
                    if ('' === $ms[1][$j]) {
                        $value = [$value];
                    } else {
                        $value = [$ms[1][$j] => $value];
                    }
                }
                if (isset($_GET[$name]) && is_array($_GET[$name])) {
                    $value = Unit::mergeArray($_GET[$name], $value);
                }
                $_REQUEST[$name] = $_GET[$name] = $value;
            } else {
                $_REQUEST[$key] = $_GET[$key] = $value;
            }
        }
    }

    /**
     * 创建URL
     * @param string $route
     * @param array $params
     * @param string $ampersand
     * @return string
     * @throws \Exception
     */
    public function createUrl($route, $params = [], $ampersand = '&')
    {
        unset($params[$this->routeVar]);
        foreach ($params as $i => $param) {
            ($param === null) && ($params[$i] = '');
        }
        // 锚点处理
        if (isset($params['#'])) {
            $anchor = '#' . $params['#'];
            unset($params['#']);
        } else {
            $anchor = '';
        }
        $route = trim($route, '/');
        foreach ($this->_rules as $i => $rule) {
            if (($url = $rule->createPathInfo($this, $route, $params, $ampersand)) !== false) {
                return $this->getBaseUrl() . '/' . $url . $anchor;
            }
        }
        if ($this->throwNotMatchRule) {
            // 不匹配规则时抛出异常
            throw new Exception(str_cover('创建URL时，路由"{route}"找不到对应的规则，请确认路由或规则是否正确', [
                '{route}' => $route,
            ]), 100900102);
        } else {
            return $this->createUrlDefault($route, $params, $ampersand) . $anchor;
        }
    }

    /**
     * 无 urlRule 对应时对URL的创建
     * @param string $route
     * @param array $params
     * @param string $ampersand
     * @return string
     * @throws \Exception
     */
    protected function createUrlDefault($route, $params, $ampersand)
    {
        if ($this->urlFormat === self::PATH_FORMAT) {
            $url = rtrim($this->getBaseUrl() . '/' . $route, '/');
            if ($this->appendParams) {
                $url = rtrim($url . '/' . $this->createPathInfo($params, '/', '/'), '/');
                return $route === '' ? $url : $url . $this->urlSuffix;
            } else {
                if ($route !== '') {
                    $url .= $this->urlSuffix;
                }
                $query = $this->createPathInfo($params, '=', $ampersand);
                return $query === '' ? $url : $url . '?' . $query;
            }
        } else {
            $url = $this->getBaseUrl();
            if (!$this->showScriptName) {
                $url .= '/';
            }
            if ($route !== '') {
                $url .= '?' . $this->routeVar . '=' . $route;
                if (($query = $this->createPathInfo($params, '=', $ampersand)) !== '') {
                    $url .= $ampersand . $query;
                }
            } elseif (($query = $this->createPathInfo($params, '=', $ampersand)) !== '') {
                $url .= '?' . $query;
            }
            return $url;
        }
    }

    /**
     * 创建参数的pathInfo串
     * @param array $params
     * @param string $equal
     * @param string $ampersand
     * @param string $key
     * @return string
     */
    public function createPathInfo($params, $equal, $ampersand, $key = null)
    {
        $pairs = [];
        foreach ($params as $k => $v) {
            if (null !== $key) {
                $k = $key . '[' . $k . ']';
            }
            if (is_array($v)) {
                $pairs[] = $this->createPathInfo($v, $equal, $ampersand, $k);
            } else {
                $pairs[] = urlencode($k) . $equal . urlencode($v);
            }
        }
        return implode($ampersand, $pairs);
    }

    /**
     * 获取应用的 baseUrl
     * @return string
     * @throws \Exception
     */
    public function getBaseUrl()
    {
        if (null === $this->_baseUrl) {
            if ($this->showScriptName) {
                $this->_baseUrl = Request::httpRequest()->getScriptUrl();
            } else {
                $this->_baseUrl = Request::httpRequest()->getBaseUrl();
            }
        }
        return $this->_baseUrl;
    }
}