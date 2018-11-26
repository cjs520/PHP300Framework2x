<?php

namespace Framework\Library\Process;

use Framework\Library\Interfaces\RouterInterface as RouterInterfaces;

/**
 * 系统路由
 * Class Router
 * @package Framework\Library\Process
 */
class Router implements RouterInterfaces
{

    /**
     * 用户请求地址
     * @var string
     */
    static public $requestUrl;

    /**
     * 路由配置信息
     * @var array
     */
    private $RouteConfig = [];

    /**
     * 初始化构造
     * Router constructor.
     */
    public function __construct()
    {
        if (isset($_SERVER['PATH_INFO'])) {
            $this->RouteConfig = \Framework\App::$app->get('Config')->get('Router');
            self::$requestUrl = $_SERVER['PATH_INFO'];
            $this->Route();
            $this->Matching();
        } else {
            $this->TraditionUrl();
        }
    }

    /**
     * 默认路由
     * @return mixed|void
     */
    public function Route()
    {
        if (strpos(self::$requestUrl, '?')) {
            preg_match_all('/^\/(.*)\?/', self::$requestUrl, $Url);
        } else {
            preg_match_all('/^\/(.*)/', self::$requestUrl, $Url);
        }
        if (empty($Url[1][0])) return;
        $Url = $Url[1][0];
        $Url = explode('/', $Url);
        if (count($Url) > 0) {
            $extend = explode('.', end($Url));
            $ResourcesList = ['js', 'css', 'png', 'jpg', 'jpeg', 'bmp', 'ico', 'txt', 'gif', 'zip', 'rar', '7z', 'exe', 'msi', 'wav', 'mp3', 'avi', 'flv', 'md', 'json', 'ini'];
            if (count($extend) > 1 && in_array(end($extend), $ResourcesList)) {
                return;
            }
        }
        \Framework\App::$app->get('Visit')->bind($Url);
    }

    /**
     * 路由匹配
     */
    private function Matching()
    {
        if (self::$requestUrl == '') {
            $Url = '/';
        } else {
            $Url = '/' . ucwords(Visit::$param['Project']) . '/' . ucwords(Visit::$param['Controller']) . '/' . Visit::$param['Function'];
        }
        $Url = strtolower($Url);
        if (count($this->RouteConfig) > 0 && isset($this->RouteConfig[$Url])) {
            $function = $this->RouteConfig[$Url];
            if (gettype($function) == 'object') {
                \Framework\App::$app->get('ReturnHandle')->Output($function());
                die();
            }
        }
    }

    /**
     * 传统URL请求匹配
     */
    public function TraditionUrl()
    {
        $Project = get(Visit::$request['Project']);
        $Controller = get(Visit::$request['Controller']);
        $Function = get(Visit::$request['Function']);
        if (!empty($Project)) {
            Visit::$param['Project'] = $Project;
        }
        if (!empty($Controller)) {
            Visit::$param['Controller'] = $Controller;
        }
        if (!empty($Function)) {
            Visit::$param['Function'] = $Function;
        }
    }

}