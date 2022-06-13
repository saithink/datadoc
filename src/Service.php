<?php

namespace saithink\datadoc;

use think\Route;
use think\Service as TpService;

class Service extends TpService
{
    public function boot()
    {
        $this->registerRoutes(function (Route $route) {
            $route->get('datadoc/docs', "\\saithink\\datadoc\\controller\\Index@index");
        });
    }
}
