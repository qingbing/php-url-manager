# php-url-manager
## 描述
组件'url-manager' : url-pathinfo的相关解析和操作

## 注意事项
 - urlManager的参数配置参考 qingbing/php-config 组件
 - urlManager 强依赖组建 qingbing/request来获取pathinfo和baseUrl等
 - urlManager 的规则配置参考 url-manager.php 示例
 - urlManager 的缓存配置为缓存的实例化字符串代码，在实例化时会通过eval来转换，如果规则不缓存，配置为空即可
 - 通过 \UrlManager::getInstance()->parseUrl() 获取规则下的真实的 route
 - 通过 \UrlManager::getInstance()->createUrl() 创建规则下新的URL


## 组件使用
```php

// 获取 urlManager 实例
$urlManager = UrlManager::getInstance('url-manager');

// 换取 urlRule 下的 pathinfo
$pathinfo = $urlManager->parseUrl();
var_dump($pathinfo);
var_dump($_GET);
var_dump('===================');

```

## 三级路由
### 1. url配置
```php
return [
    'routeVar' => "r", // get 模式下路由的标志
    'urlSuffix' => ".shtml", // path 模式下链接的后缀名
    'showScriptName' => true, // path 创建URL访问链接时是否显示脚本名
    'appendParams' => true, // 参数是否增加在 pathInfo 后
    'cacheInstanceString' => "\Components\FileCache::getInstance('cache-file');", // cache 实例的实例化字符串代码，设置为空表示不缓存规则
    'caseSensitive' => "false", // 链接的地址是否区分大小写
    'urlFormat' => "path", // 路由的显示模式，get和path两种
    'throwNotMatchRule' => true, // 当无匹配的url规则时是否抛出异常
    'rules' => [
        // 两级路由
        [
            'pattern' => '',
            'route' => 'site/index',
//            'defaultParams' => ['area' => 'chengdu'],
        ], [
            'pattern' => '<controller:\w+>',
            'route' => '<controller>/index',
        ], [
            'pattern' => '<controller:\w+>/add/*',
            'route' => '<controller>/add',
        ], [
            'pattern' => '<controller:\w+>/<action:(edit|delete)>/<id:\d+>/*',
            'route' => '<controller>/<action>',
        ], [
            'pattern' => '<controller:\w+>/<id:\d+>/*',
            'route' => '<controller>/view',
        ], [
            'pattern' => '<controller:\w+>/list/*',
            'route' => '<controller>/index',
        ], [
            'pattern' => '<controller:\w+>/<action:\w+>/*',
            'route' => '<controller>/<action>',
        ],

        // 三级路由
        /*[
            'pattern' => '',
            'route' => 'home/default/index',
        ], [
            'pattern' => '<module:\w+>',
            'route' => '<module>/default/index',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>',
            'route' => '<module>/<controller>/index',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>/add/*',
            'route' => '<module>/<controller>/add',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>/<action:(edit|delete)>/<id:\d+>/*',
            'route' => '<module>/<controller>/<action>',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>/<id:\d+>/*',
            'route' => '<module>/<controller>/view',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>/list/*',
            'route' => '<module>/<controller>/index',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>/<action:\w+>/*',
            'route' => '<module>/<controller>/<action>',
        ],*/
    ],
];
```
### 2. 使用方法
```php

// 创建 URL
$url = $urlManager->createUrl('home/default/index');
var_dump($url);

$url = $urlManager->createUrl('admin/default/index');
var_dump($url);

$url = $urlManager->createUrl('admin/good/add');
var_dump($url);

$url = $urlManager->createUrl('admin/good/add', ['id' => 22]);
var_dump($url);

$url = $urlManager->createUrl('admin/good/delete', ['sex' => 'nv', 'id' => 22]);
var_dump($url);

$url = $urlManager->createUrl('admin/good/edit', ['id' => 22]);
var_dump($url);

$url = $urlManager->createUrl('admin/good/view', ['id' => 22]);
var_dump($url);

$url = $urlManager->createUrl('admin/good/index');
var_dump($url);

$url = $urlManager->createUrl('admin/good/index', ['id' => 5, 'sex' => 'nv']);
var_dump($url);

$url = $urlManager->createUrl('admin/good/list');
var_dump($url);

$url = $urlManager->createUrl('admin/good/list', ['id' => 5, 'sex' => 'nan']);
var_dump($url);

$url = $urlManager->createUrl('admin/site/test', ['id' => 5]);
var_dump($url);

```


## 二级路由
### 1. url配置
```php
; URL 的规则 - 二级路由（<controller>/<action>）
    'rules' => [
        // 两级路由
        [
            'pattern' => '',
            'route' => 'site/index',
//            'defaultParams' => ['area' => 'chengdu'],
        ], [
            'pattern' => '<controller:\w+>',
            'route' => '<controller>/index',
        ], [
            'pattern' => '<controller:\w+>/add/*',
            'route' => '<controller>/add',
        ], [
            'pattern' => '<controller:\w+>/<action:(edit|delete)>/<id:\d+>/*',
            'route' => '<controller>/<action>',
        ], [
            'pattern' => '<controller:\w+>/<id:\d+>/*',
            'route' => '<controller>/view',
        ], [
            'pattern' => '<controller:\w+>/list/*',
            'route' => '<controller>/index',
        ], [
            'pattern' => '<controller:\w+>/<action:\w+>/*',
            'route' => '<controller>/<action>',
        ],

        // 三级路由
        /*[
            'pattern' => '',
            'route' => 'home/default/index',
        ], [
            'pattern' => '<module:\w+>',
            'route' => '<module>/default/index',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>',
            'route' => '<module>/<controller>/index',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>/add/*',
            'route' => '<module>/<controller>/add',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>/<action:(edit|delete)>/<id:\d+>/*',
            'route' => '<module>/<controller>/<action>',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>/<id:\d+>/*',
            'route' => '<module>/<controller>/view',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>/list/*',
            'route' => '<module>/<controller>/index',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>/<action:\w+>/*',
            'route' => '<module>/<controller>/<action>',
        ],*/
    ],

```

### 2. 使用方法
```php

// 二级创建 URL
$url = $urlManager->createUrl('site/index');
var_dump($url);

$url = $urlManager->createUrl('good/add');
var_dump($url);

$url = $urlManager->createUrl('good/add', ['id' => 22]);
var_dump($url);

$url = $urlManager->createUrl('good/delete', ['sex' => 'nv', 'id' => 22]);
var_dump($url);

$url = $urlManager->createUrl('good/edit', ['id' => 22]);
var_dump($url);

$url = $urlManager->createUrl('good/view', ['id' => 22]);
var_dump($url);

$url = $urlManager->createUrl('good/index');
var_dump($url);

$url = $urlManager->createUrl('good/index', ['id' => 5, 'sex' => 'nv']);
var_dump($url);

$url = $urlManager->createUrl('good/list');
var_dump($url);

$url = $urlManager->createUrl('good/list', ['id' => 5, 'sex' => 'nan']);
var_dump($url);

$url = $urlManager->createUrl('site/test', ['id' => 5]);
var_dump($url);

```

## ====== 异常代码集合 ======

异常代码格式：1009 - XXX - XX （组件编号 - 文件编号 - 代码内异常）
```
 - 100900101 : "{pathInfo}"找不到对应的URL解析规则，不能确定路由
 - 100900102 : 创建URL时，路由"{route}"找不到对应的规则，请确认路由或规则是否正确
```