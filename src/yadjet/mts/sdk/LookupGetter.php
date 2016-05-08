<?php

namespace yadjet\mts\sdk;

use yii\db\Query;

class LookupGetter implements DataGetterInterface
{

    use GetterTrait;

    public static function parseQueryCondition($params = [])
    {
        if (!isset($params['label'])) {
            $params['label'] = '*';
        }
    }

    public static function all($params = [])
    {
        throw new \yii\base\NotSupportedException();
    }

    public static function cell($params = [])
    {
        $params = self::parseQueryCondition($params);

        return (new Query())
                ->from('{{%lookup}}')
                ->where(['tenant_id' => 1, 'label' => strtolower($params['label'])])
                ->all();
    }

    public static function one($params = [])
    {
        return (new Query())
                ->from('{{%lookup}}')
                ->where(['tenant_id' => 1, 'label' => strtolower($params['label'])])
                ->one();
    }

}
