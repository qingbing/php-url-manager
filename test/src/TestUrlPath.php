<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2018-10-27
 * Version      :   1.0
 */

namespace Test;

use TestCore\Tester;
use UrlManager;

class TestUrlPath extends Tester
{
    /**
     * 执行函数
     * @throws \Exception
     */
    public function run()
    {
        // 获取 urlManager 实例
        $urlManager = UrlManager::getInstance('url-manager');

        // 换取 urlRule 下的 pathinfo
        $pathinfo = $urlManager->parseUrl();
        var_dump($pathinfo);
        var_dump($_GET);
        var_dump('===================');

//        // 三级创建 URL
//        $url = $urlManager->createUrl('home/default/index');
//        var_dump($url);
//
//        $url = $urlManager->createUrl('admin/default/index');
//        var_dump($url);
//
//        $url = $urlManager->createUrl('admin/good/add');
//        var_dump($url);
//
//        $url = $urlManager->createUrl('admin/good/add', ['id' => 22]);
//        var_dump($url);
//
//        $url = $urlManager->createUrl('admin/good/delete', ['sex' => 'nv', 'id' => 22]);
//        var_dump($url);
//
//        $url = $urlManager->createUrl('admin/good/edit', ['id' => 22]);
//        var_dump($url);
//
//        $url = $urlManager->createUrl('admin/good/view', ['id' => 22]);
//        var_dump($url);
//
//        $url = $urlManager->createUrl('admin/good/index');
//        var_dump($url);
//
//        $url = $urlManager->createUrl('admin/good/index', ['id' => 5, 'sex' => 'nv']);
//        var_dump($url);
//
//        $url = $urlManager->createUrl('admin/good/list');
//        var_dump($url);
//
//        $url = $urlManager->createUrl('admin/good/list', ['id' => 5, 'sex' => 'nan']);
//        var_dump($url);
//
//        $url = $urlManager->createUrl('admin/site/test', ['id' => 5]);
//        var_dump($url);
        


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
    }
}