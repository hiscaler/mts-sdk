<?php

namespace yadjet\mts\sdk;

trait GetterTrait
{

    private static function parseParams($params, $returnType = self::RETURN_LIST)
    {
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

            case self::RETURN_LIST:
                $defaultParams = [
                    'offset' => 0,
                    'limit' => 10,
                    'orderBy' => 'id.desc',
                ];
                break;

            default:
                $defaultParams = [];
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

        $totalCount = $query->count();
        $pageCount = (($totalCount + $pageSize - 1) / $pageSize);
        $meta['totalCount'] = $totalCount;
        $meta['pageCount'] = $pageCount;

        return $meta;
    }

}
