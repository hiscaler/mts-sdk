<?php

namespace yadjet\mts\sdk;

use yii\db\Query;

class FriendlyLinkGetter extends DataGetter implements DataGetterInterface
{

    public static function parseQuery($fields = '*', $where = [], $orderBy = 'ordering.asc', $offset = 0, $limit = 10)
    {
        $condition = [
            'tenant_id' => self::getConstantValue('TENANT_ID'),
            'enabled' => self::BOOLEAN_TRUE
        ];
        if (isset($where['group'])) {
            $condition['group'] = (int) $where['group'];
        }
        if (isset($where['type'])) {
            $type = strtolower($where['type']);
            if (in_array($type, ['picture', 'text'])) {
                $condition['type'] = $type == 'picture' ? 1 : 0;
            }
        }

        return (new Query())
                ->select($fields)
                ->from('{{%friendly_link}}')
                ->where($condition)
                ->orderBy(self::parseOrderBy($orderBy))
                ->offset($offset)
                ->limit($limit);
    }

    public static function one($params = [])
    {
        throw new \yii\base\NotSupportedException();
    }

}
