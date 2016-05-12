<?php

namespace yadjet\mts\sdk;

/**
 * 数据拉取
 * 
 * @author hiscaler <hiscaler@gmail.com>
 */
class DataGetter
{

    use DataGetterTrait;

    const RETURN_ALL = 'ALL';
    const RETURN_ROWS = 'ROWS';
    const RETURN_ONE = 'ONE';
    
    const BOOLEAN_FALSE = 0;
    const BOOLEAN_TRUE = 1;

    public static function all($fields = '*', $where = [], $orderBy = 'ordering.asc', $page = 1, $pageSize = 10)
    {
        $query = static::parseQuery($fields, $where, $orderBy, ($page - 1) * $pageSize, $pageSize);

        return [
            'items' => $query->all(),
            '_meta' => self::paginationMeta($query, $page, $pageSize),
        ];
    }

    public static function rows($fields = '*', $where = [], $orderBy = 'ordering.asc', $offset = 0, $limit = 10)
    {
        return static::parseQuery($fields, $where, $orderBy, $offset, $limit)->all();
    }

}
