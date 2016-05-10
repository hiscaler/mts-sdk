<?php

namespace yadjet\mts\sdk;

trait GetterTrait
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
            $defaultParams['fields'] = '*';
        }
        if (!isset($params['condition'])) {
            $params['condition'] = [];
        }
        if (!isset($params['condition']['tenant_id'])) {
            $params['condition']['tenantId'] = defined('MTS_SDK_TENANT_ID') ? constant('MTS_SDK_TENANT_ID') : 0;
        }

        foreach ($defaultParams as $key => $value) {
            if (!isset($params[$key])) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    private static function parseOrderBy($orderBy)
    {
        $res = [];
        if ($orderBy) {
            $rawData = explode(',', trim($orderBy));
            foreach ($rawData as $value) {
                if (strpos($value, '.') !== false) {
                    $t = explode('.', $value);
                    $res[$t[0]] = $t[1];
                } else {
                    $res[$orderBy] = SORT_DESC;
                }
            }
        }

        return $res;
    }

    public static function paginationMeta(\yii\db\Query $query, $config)
    {
        $pageSize = $config['pageSize'];
        $meta = [
            'currentPage' => $config['currentPage'],
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

    protected static function getConstantValue($name, $defaultValue = null)
    {
        $name = 'MTS_SDK_' . strtoupper($name);

        return defined($name) ? constant($name) : $defaultValue;
    }

}
