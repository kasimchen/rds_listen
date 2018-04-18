<?php
/**
 * Created by PhpStorm.
 * User: chenhuan
 * Date: 2018/4/17
 * Time: 下午1:44
 */

namespace Onemena\RdsListen\Facades;
use Illuminate\Support\Facades\Facade;
class RdsListen extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rdslisten';
    }
}