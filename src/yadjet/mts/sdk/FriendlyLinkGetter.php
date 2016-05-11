<?php

namespace yadjet\mts\sdk;

class FriendlyLinkGetter implements DataGetterInterface
{

    use GetterTrait;

    public static function parseQuery($params = [])
    {
        $rawCondition = $params['condition'];
        $where = [];
        if (isset($rawCondition['group'])) {
            $where['group'] = (int) $rawCondition['group'];
        }
        if (isset($rawCondition['type'])) {
            $type = strtolower($rawCondition['type']);
            if (in_array($type, ['picture', 'text'])) {
                $where['type'] = $type == 'picture' ? 1 : 0;
            }
        }
        $params['condition'] = $where;

        return $params;
    }

    public static function all($params = [])
    {
        $params = static::parseParams($params, self::RETURN_ALL);
        $query = self::parseQuery($params);

        return self::toAll($query, $params);
    }

    public static function rows($params = [])
    {
        $params = static::parseParams($params, self::RETURN_ALL);

        return self::parseQuery($params)->all();
    }

    public static function one($params = [])
    {
        throw new \yii\base\NotSupportedException();
    }

}
