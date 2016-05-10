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
        $params = self::parseParams($params, self::RETURN_ALL);
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
        $query = (new Query())->select($params['fields'])
            ->from('{{%friendly_link}}')
            ->offset($params['offset'])
            ->limit($params['limit'])
            ->orderBy(self::parseOrderBy($params['orderBy']));

        return self::toAll($query, $params);
    }

    public static function friendlyLinkList($params = [])
    {
        $params = self::parseParams($params, self::RETURN_LIST);
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
        return (new Query())->select($params['fields'])
                ->from('{{%friendly_link}}')
                ->offset($params['offset'])
                ->limit($params['limit'])
                ->orderBy(self::parseOrderBy($params['orderBy']))
                ->all();
    }

}