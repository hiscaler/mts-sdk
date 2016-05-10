<?php

namespace yadjet\mts\sdk;

interface DataGetterInterface
{

    const RETURN_ALL = 'ALL';
    const RETURN_ROWS = 'ROWS';
    const RETURN_ONE = 'ONE';
    const BOOLEAN_FALSE = 0;
    const BOOLEAN_TRUE = 1;

    public static function parseQuery($params = []);

    public static function all($params = []);

    public static function rows($params = []);

    public static function one($params = []);
}
