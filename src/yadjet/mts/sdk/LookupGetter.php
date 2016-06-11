<?php

namespace yadjet\mts\sdk;

use Yii;
use yii\db\Query;

class LookupGetter extends DataGetter
{

    const RETURN_TYPE_INTEGER = 0;
    const RETURN_TYPE_STRING = 1;

    public static function rows($labels)
    {
        $rawData = (new Query())
            ->select(['value', 'return_type', 'label'])
            ->from('{{%lookup}}')
            ->where(['tenant_id' => self::getTenantId(), 'label' => array_keys($labels)])
            ->indexBy('label')
            ->all();
        foreach ($labels as $key => $defautValue) {
            if (isset($rawData[$key])) {
                $value = $rawData[$key]['value'];
                switch ($rawData[$key]['return_type']) {
                    case static::RETURN_TYPE_INTEGER:
                        $value = (int) $value;
                        break;

                    case static::RETURN_TYPE_STRING:
                        $value = (string) $value;
                        break;
                }
                $labels[$key] = $value;
            } else {
                $labels[$key] = $defautValue;
            }
        }

        return $labels;
    }

    public static function one($label, $defaultValue = null)
    {
        $rawData = Yii::$app->getDb()->createCommand('SELECT [[value]], [[return_type]] FROM {{%lookup}} WHERE [[tenant_id]] = :tenantId AND [[label]] = :label AND [[enabled]] = :enabled')->bindValues([
                ':label' => self::parseLabel($label),
                ':tenantId' => self::getTenantId(),
                ':enabled' => self::BOOLEAN_TRUE
            ])->queryOne();
        if ($rawData === false) {
            $value = $defaultValue;
        } else {
            $value = $rawData['value'];
            switch ($rawData['return_type']) {
                case static::RETURN_TYPE_INTEGER:
                    $value = (int) $value;
                    break;

                case static::RETURN_TYPE_STRING:
                    $value = (string) $value;
                    break;
            }
        }

        return $value;
    }

}
