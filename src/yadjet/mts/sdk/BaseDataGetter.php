<?php

namespace yadjet\mts\sdk;

use yii\db\Query;

/**
 * 数据拉取
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseDataGetter
{

    use GetterTrait;

    const RETURN_ALL = 'ALL';
    const RETURN_LIST = 'LIST';
    const RETURN_ONE = 'ONE';

    public static function friendlyLinkAll($params = [])
    {
        $params = self::parseParams($params);
        $where = [];
        if (isset($params['group'])) {
            $where['group'] = (int) $params['group'];
        }
        if (isset($params['type'])) {
            $type = strtolower($params['type']);
            if (in_array($type, ['picture', 'text'])) {
                $where['type'] = $type == 'picture' ? 1 : 0;
            }
        }
        $query = (new Query())->select()
            ->offset($params['offset'])
            ->limit($params['limit'])
            ->orderBy(self::parseOrderBy($params['orderBy']));

        return [
            'items' => $query->all(),
            '_meta' => self::paginationMeta($query)
        ];
    }

    public static function friendlyLinkList($params = [])
    {
        return [];
    }

}
