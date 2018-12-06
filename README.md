# php-url-manager
## 描述
Url 的pathinfo解析和相关操作

## 注意事项
 - urlManager的参数配置参考 qingbing/php-config 组件
 - urlManager 强依赖组建 qingbing/request来获取pathinfo和baseUrl等
 - urlManager 的规则配置参考 conf/url-manager.ini->"[url.rules]"
 - urlManager 的缓存配置为缓存的实例化字符串代码，在实例化时会通过eval来转换，如果规则不缓存，配置为空即可
 - 通过 \UrlManager::getInstance()->parseUrl() 获取规则下的真实的 route
 - 通过 \UrlManager::getInstance()->createUrl() 创建规则下新的URL


## 组件使用
```php

// 获取 urlManager 实例
$urlManager = UrlManager::getInstance();

// 换取 urlRule 下的 pathinfo
$pathinfo = $urlManager->parseUrl();
var_dump($pathinfo);
var_dump($_GET);
var_dump('===================');

```

## 三级路由
### 1. url配置
```ini
[url.rules]
url.rule.pattern[] = "";
url.rule.route[] = "home/default/index";

url.rule.pattern[] = "<module:\w+>";
url.rule.route[] = "<module>/default/index";

url.rule.pattern[] = "<module:\w+>/<controller:\w+>";
url.rule.route[] = "<module>/<controller>/index";

url.rule.pattern[] = "<module:\w+>/<controller:\w+>/add/*";
url.rule.route[] = "<module>/<controller>/add";

url.rule.pattern[] = "<module:\w+>/<controller:\w+>/<action:(edit|delete)>/<id:\d+>/*";
url.rule.route[] = "<module>/<controller>/<action>";

url.rule.pattern[] = "<module:\w+>/<controller:\w+>/<id:\d+>/*";
url.rule.route[] = "<module>/<controller>/view";

url.rule.pattern[] = "<module:\w+>/<controller:\w+>/list/*";
url.rule.route[] = "<module>/<controller>/index";

url.rule.pattern[] = "<module:\w+>/<controller:\w+>/<action:\w+>/*";
url.rule.route[] = "<module>/<controller>/<action>";

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
```ini
; URL 的规则 - 二级路由（<controller>/<action>）
[url.rules]
url.rule.pattern[] = "";
url.rule.route[] = "site/index";

url.rule.pattern[] = "<controller:\w+>";
url.rule.route[] = "<controller>/index";

url.rule.pattern[] = "<controller:\w+>/add/*";
url.rule.route[] = "<controller>/add";

url.rule.pattern[] = "<controller:\w+>/<action:(edit|delete)>/<id:\d+>/*";
url.rule.route[] = "<controller>/<action>";

url.rule.pattern[] = "<controller:\w+>/<id:\d+>/*";
url.rule.route[] = "<controller>/view";

url.rule.pattern[] = "<controller:\w+>/list/*";
url.rule.route[] = "<controller>/index";

url.rule.pattern[] = "<controller:\w+>/<action:\w+>/*";
url.rule.route[] = "<controller>/<action>";
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

异常代码格式：1010 - XXX - XX （组件编号 - 文件编号 - 代码内异常）
```
 - 101000101 : "{pathInfo}"找不到对应的URL解析规则，不能确定路由
 - 101000102 : 创建URL时，路由"{route}"找不到对应的规则，请确认路由或规则是否正确
```

