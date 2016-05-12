<?php

namespace yadjet\mts\sdk;

/**
 * 数据拉取
 * 
 * @author hiscaler <hiscaler@gmail.com>
 */
class DataGetter
{

    const RETURN_ALL = 'ALL';
    const RETURN_ROWS = 'ROWS';
    const RETURN_ONE = 'ONE';
    
    const BOOLEAN_FALSE = 0;
    const BOOLEAN_TRUE = 1;

    protected static function getConstantValue($name, $defaultValue = null)
    {
        $name = 'MTS_SDK_' . strtoupper($name);

        return defined($name) ? constant($name) : $defaultValue;
    }

}
