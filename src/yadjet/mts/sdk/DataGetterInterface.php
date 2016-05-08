<?php

namespace yadjet\mts\sdk;

interface DataGetterInterface
{

    const RETURN_ALL = 'ALL';
    const RETURN_LIST = 'LIST';
    const RETURN_ONE = 'ONE';

    public static function parseQueryCondition($params = []);

    public static function all($params = []);

    public static function cell($params = []);

    public static function one($params = []);
}
