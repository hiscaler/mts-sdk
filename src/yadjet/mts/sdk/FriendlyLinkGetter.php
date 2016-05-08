<?php

namespace yadjet\mts\sdk;

use yii\db\Query;

class FriendlyLinkGetter implements DataGetterInterface
{

    use GetterTrait;

    public static function parseQueryCondition($params = [])
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

        return $params;
    }

    public static function all($params = [])
    {
        $params = self::parseQueryCondition($params);
        $query = (new Query())->select($params['fields'])
            ->from('{{%friendly_link}}')
            ->offset($params['offset'])
            ->limit($params['limit'])
            ->orderBy(self::parseOrderBy($params['orderBy']));

        return self::toAll($query, $params);
    }

    public static function cell($params = [])
    {
        $params = self::parseQueryCondition($params = array());
        return (new Query())->select($params['fields'])
                ->from('{{%friendly_link}}')
                ->offset($params['offset'])
                ->limit($params['limit'])
                ->orderBy(self::parseOrderBy($params['orderBy']))
                ->all();
    }

    public static function one($params = [])
    {
        throw new \yii\base\NotSupportedException();
    }

}
