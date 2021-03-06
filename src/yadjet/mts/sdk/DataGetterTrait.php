<?php

namespace yadjet\mts\sdk;

trait DataGetterTrait
{

    public static function parseParams($params, $returnType = self::RETURN_ROWS)
    {
        if (!is_array($params)) {
            throw new \yii\base\InvalidParamException('参数无效。');
        }

        switch ($returnType) {
            case self::RETURN_ALL:
                $defaultParams = [
                    'page' => 1,
                    'pageSize' => 10,
                    'currentPage' => 1,
                    'offset' => 0,
                    'limit' => 10,
                    'orderBy' => 'id.desc',
                ];

                break;

            case self::RETURN_ROWS:
                $defaultParams = [
                    'offset' => 0,
                    'limit' => 10,
                    'orderBy' => 'id.desc',
                ];
                break;

            default:
                $defaultParams = [];
        }

        if (!isset($params['fields'])) {
            $defaultParams['fields'] = 't.*';
        } else {
            foreach ($params['fields'] as &$field) {
                $field = "t.$field";
            }
        }
        if (!isset($params['condition'])) {
            $params['condition'] = [];
        }
        if (!isset($params['condition']['tenant_id'])) {
            $params['condition']['t.tenant_id'] = defined('MTS_SDK_TENANT_ID') ? constant('MTS_SDK_TENANT_ID') : 0;
        }

        foreach ($defaultParams as $key => $value) {
            if (!isset($params[$key])) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    public static function fixQueryFields($fields, $addT = true)
    {
        if (!is_array($fields)) {
            $fields = explode(',', $fields);
        }
        if ($addT) {
            foreach ($fields as $i => $field) {
                $fields[$i] = "t.$field";
            }
        }

        return $fields;
    }

    public static function parseOrderBy($orderBy)
    {
        $res = [];
        if ($orderBy) {
            $rawData = explode(',', trim($orderBy));
            foreach ($rawData as $value) {
                if (strpos($value, '.') !== false) {
                    $t = explode('.', $value);
                    $res[$t[0]] = strtolower($t[1]) == 'asc' ? SORT_ASC : SORT_DESC;
                } else {
                    $res[$orderBy] = SORT_ASC;
                }
            }
        }

        return $res;
    }

    public static function paginationMeta(\yii\db\Query $query, $page = 1, $pageSize = 10)
    {
        $meta = [
            'currentPage' => $page,
            'perPage' => $pageSize,
        ];

        $totalCount = (int) $query->count();
        $pageCount = (int) (($totalCount + $pageSize - 1) / $pageSize);
        $meta['totalCount'] = $totalCount;
        $meta['pageCount'] = $pageCount;

        return $meta;
    }

    public static function toAll(\yii\db\Query $query, $params)
    {
        return [
            'items' => $query->all(),
            '_meta' => self::paginationMeta($query, $params)
        ];
    }

    public static function all($fields = '*', $where = [], $orderBy = 'id.asc', $page = 1, $pageSize = 10)
    {
        $query = static::parseQuery($fields, $where, $orderBy, ($page - 1) * $pageSize, $pageSize);

        return [
            'items' => $query->all(),
            '_meta' => self::paginationMeta($query, $page, $pageSize),
        ];
    }

    public static function rows($fields = '*', $where = [], $orderBy = 'id.asc', $offset = 0, $limit = 10)
    {
        return static::parseQuery($fields, $where, $orderBy, $offset, $limit)->all();
    }

}
