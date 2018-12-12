<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2018-10-27
 * Version      :   1.0
 */

namespace Test;

use Components\Request;
use TestCore\Tester;

class TestUrlManager extends Tester
{
    /**
     * 执行函数
     * @throws \Exception
     */
    public function run()
    {

        $scriptName = Request::httpRequest()->getScriptName();

        $R = [];
        array_push($R, "{$scriptName}/good/add?c=TestUrlPath");
        array_push($R, "{$scriptName}/good/edit/5?c=TestUrlPath");
        array_push($R, "{$scriptName}/good/delete/5?c=TestUrlPath");
        array_push($R, "{$scriptName}/good/5?c=TestUrlPath");
        array_push($R, "{$scriptName}/good/list?c=TestUrlPath");
        array_push($R, "{$scriptName}/good?c=TestUrlPath");
        array_push($R, "{$scriptName}?c=TestUrlPath");
        array_push($R, "{$scriptName}/good/product?c=TestUrlPath");

//        array_push($R, "{$scriptName}/admin/good/add?c=TestUrlPath");
//        array_push($R, "{$scriptName}/admin/good/edit/5?c=TestUrlPath");
//        array_push($R, "{$scriptName}/admin/good/delete/5?c=TestUrlPath");
//        array_push($R, "{$scriptName}/admin/good/5?c=TestUrlPath");
//        array_push($R, "{$scriptName}/admin/good/list?c=TestUrlPath");
//        array_push($R, "{$scriptName}/admin/good?c=TestUrlPath");
//        array_push($R, "{$scriptName}/admin?c=TestUrlPath");
//        array_push($R, "{$scriptName}/admin/good/product?c=TestUrlPath");

        foreach ($R as $url) {
            echo "<a href='{$url}'>{$url}</a>\n\n";
        }

    }
}